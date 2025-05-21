<?php

// ### 配置开始 ###
// 1. 在此处粘贴你的加密字符串:
$input_encrypted_string = ""; // <--- 在此处粘贴你的加密数据

// 2. 设置解密密钥 (默认为冰蝎常用的默认密钥 - e45e329feb5d925b)
$key = '05c1cc9c2deafb75'; // <--- 如果需要，请更改密钥

// 3. 设置HTTP类型 (与某些XOR解密场景相关，例如某些客户端在XOR前会进行Base64编码)
//    通常，如果数据来自类似 Python requests 的客户端（可能进行了Base64编码），则为 'requests'
$http_type = 'requests'; // <--- 如果需要，请更改 HTTP_TYPE
// ### 配置结束 ###

echo "开始处理解密...\n\n"; // 输出提示信息

$post_raw = $input_encrypted_string; // 将输入的加密字符串赋值给 $post_raw

// --- 函数定义 (保持不变) ---

/**
 * AES 解密函数
 * @param string $post_raw_data 原始加密数据（可能包含干扰字符）
 * @param string $decryption_key 解密密钥
 * @return string 解密后的数据，或者 'no' 表示解密失败
 */
function aes_convert_str($post_raw_data, $decryption_key){
    // 尝试在输入中查找符合Base64特征的字符串 (通常长度较长，冰蝎AES加密后是Base64)
    preg_match('/[a-zA-Z0-9\+\=\/]{24,}/i', $post_raw_data, $post_match);
    
    if (empty($post_match[0])){
        // AES: 未找到合适的解密字符串。
        return 'no';
    }
    
    $data_to_decrypt = $post_match[0]; // 获取匹配到的疑似加密数据
    // AES: 尝试解密: $data_to_decrypt

    try {
        // 冰蝎3.0及以后版本通常使用 AES-128-CBC 模式，IV 为16个空字节 (\0)
        // 密钥长度需要是16字节 (AES-128)
        $iv = str_repeat("\0", 16); // 生成16个空字节的IV

        // openssl_decrypt 参数: 密文, 加密方法, 密钥, 选项, IV
        // OPENSSL_RAW_DATA: 返回原始数据而不是base64编码的数据
        // OPENSSL_NO_PADDING: PHP的openssl默认使用PKCS7填充，但冰蝎有时自己处理或有特定填充方式，
        //                   这里先尝试不让openssl处理填充，之后手动移除。
        $decrypted_post = openssl_decrypt(base64_decode($data_to_decrypt), "AES-128-CBC", $decryption_key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
        
        // 手动尝试移除 PKCS7 填充
        // 冰蝎的Java端在加密时可能会添加PKCS7 padding，PHP在解密时如果指定OPENSSL_NO_PADDING，则需要手动处理
        if ($decrypted_post !== false) {
            $pad_char_val = ord($decrypted_post[strlen($decrypted_post) - 1]); // 获取最后一个字节的ASCII值
            if ($pad_char_val > 0 && $pad_char_val <= 16) { // PKCS7填充的值等于填充的字节数，且不大于块大小(16)
                $valid_padding = true;
                // 检查末尾的 $pad_char_val 个字节是否都等于 $pad_char_val
                for ($i = 0; $i < $pad_char_val; $i++) {
                    if (ord($decrypted_post[strlen($decrypted_post) - 1 - $i]) != $pad_char_val) {
                        $valid_padding = false;
                        break;
                    }
                }
                if ($valid_padding) {
                    $decrypted_post = substr($decrypted_post, 0, -$pad_char_val); // 移除填充
                }
            }
        }

    } catch (\Throwable $e) {
        // AES 解密错误: $e->getMessage()
        return 'no';
    }

    if ($decrypted_post === false || $decrypted_post === '') {
        // AES: 解密失败或结果为空字符串。
        return 'no';
    }
    // AES: 解密成功。
    return $decrypted_post;
}

/**
 * XOR 解密函数
 * @param string $post_raw_data 原始加密数据
 * @param string $decryption_key 解密密钥
 * @param string $current_http_type HTTP类型，用于判断是否需要先进行Base64解码
 * @return string 解密后的数据
 */
function xor_convert_str($post_raw_data, $decryption_key, $current_http_type){
    $data_to_process = $post_raw_data;
    // XOR: 初始数据: $data_to_process

    // 如果HTTP类型是 'requests' (常见于Python客户端)，冰蝎的XOR数据通常会先进行Base64编码
    if ($current_http_type == 'requests'){
        // XOR: HTTP 类型为 'requests', 尝试 Base64 解码。
        $decoded_data = base64_decode($data_to_process, true); // true 表示严格模式
        if ($decoded_data === false) {
            // XOR: 'requests' 类型的 Base64 解码失败。使用原始数据。
            // 这意味着输入可能不是预期的Base64编码，或者Base64本身已损坏
        } else {
            $data_to_process = $decoded_data; // 使用解码后的数据进行后续XOR操作
            // XOR: Base64 解码后的数据: $data_to_process
        }
    }
    
    // 此模式移除非常特定，可能针对某些PHP环境下返回的特定错误消息被包含在加密荷载前的情况
    // 如果你的数据是干净的，这部分不会影响它。
    $pattern = '<b>Warning</b>:  session_start(): Cannot send session cache limiter - headers already sent in <b>D:\phpstudy_pro\WWW\sqli-labs\shell.php</b> on line <b>3</b><br />';
    $pos = strpos($data_to_process, $pattern);
    if($pos !== false){
        $result_after_pattern = substr($data_to_process, $pos + strlen($pattern));
        // 原始脚本有 +1， 这里移除了，通常substr(string, offset)即可。如果特定场景需要+1可以加回。
        if($result_after_pattern !== '' && $result_after_pattern !== false){
            $data_to_process = $result_after_pattern;
            // XOR: 已移除警告模式。数据现在是: $data_to_process
        }
    }
            
    $key_len = strlen($decryption_key);
    if ($key_len == 0) {
        // XOR: 错误 - 解密密钥为空。
        return "错误：XOR密钥为空。"; // 防止除以零或意外行为
    }
    $decrypted_string = "";
    for($i=0; $i<strlen($data_to_process); $i++) {
        // XOR核心操作：数据每个字节与密钥对应字节进行异或
        // $i % $key_len 确保密钥循环使用
        $decrypted_string .= $data_to_process[$i] ^ $decryption_key[$i % $key_len]; // 修正的 XOR 密钥索引
    }
    // XOR: 解密后的数据: $decrypted_string
    return $decrypted_string;
}

// --- 主要解密逻辑 ---

// 首先尝试AES解密
$decrypted_output = aes_convert_str($post_raw, $key);

// 如果AES解密失败 (返回 'no')，则尝试XOR解密
if ($decrypted_output === 'no') {
    // AES解密失败或不适用，尝试XOR解密...
    $decrypted_output = xor_convert_str($post_raw, $key, $http_type);
}

// --- 输出处理 ---
// 最终解密输出 (原始): $decrypted_output

if ($decrypted_output === 'no' || $decrypted_output === "错误：XOR密钥为空。") {
    echo "AES和XOR方法均解密失败。\n输入为: " . htmlspecialchars($post_raw) . "\n";
} elseif (preg_match('/^\{.*\}$/s', $decrypted_output)) { // 使用正则更稳健地检查是否像一个JSON对象 (包含换行)
    // 输出似乎是JSON，尝试解码...
    $raw_data = json_decode($decrypted_output, true); // true 表示返回关联数组
    if (json_last_error() === JSON_ERROR_NONE) { // 检查JSON解码是否成功
        $result = [];
        if (is_array($raw_data)) {
            foreach ($raw_data as $k => $value) {
                if (is_string($value)) { // 冰蝎的JSON值通常是Base64编码的
                    $decoded_value = base64_decode($value, true); // 严格模式解码
                    $result[$k] = ($decoded_value !== false) ? $decoded_value : "[Base64解码错误: " . htmlspecialchars($value) . "]";
                } else {
                    $result[$k] = $value; // 非字符串值保持原样
                }
            }
        }
        echo "解码后的JSON数据:\n";
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); // 格式化输出JSON
        echo "\n";
    } else {
        // JSON 解码失败。按原样输出。
        echo "解密数据 (但未能解析为JSON):\n";
        echo htmlspecialchars($decrypted_output); // 输出原始解密数据，进行HTML转义以防XSS
    }
} else {
    // 输出似乎不是JSON。处理为潜在的 '命令|base64_data' 或纯文本...
    // 冰蝎的某些响应（如文件管理、命令执行的非JSON部分）可能是这种格式
    $arr_2 = explode('|', $decrypted_output, 2); // 最多分割成2部分
    // $func = $arr_2[0]; // 原始脚本中 $func 未在后续输出逻辑中使用（如果不是JSON）
    $parm = isset($arr_2[1]) ? $arr_2[1] : $arr_2[0]; // 如果没有'|'，则 $parm 就是整个 $decrypted_output
    
    if ($parm === '') {
        // 如果参数为空，可能是由于TCP截断不完整等原因导致解密后内容不完整
        // "content is empty" base64编码
        $parm = 'Y29udGVudCBpcyBlbXB0eQ=='; 
        // 参数为空，默认为占位符。
    }

    // 这个正则表达式用于检查 $parm 是否本身就是一个Base64字符串
    // 如果 $parm 已经是base64内容，它可能会匹配自身。
    // 如果 $parm 是纯文本，它将不匹配，$parm 将被直接回显。
    preg_match('/^[a-zA-Z0-9\+\=\/]{4,}$/i', $parm, $last_result_match); // 匹配 $parm 本身是否为 Base64 编码字符串

    if (!empty($last_result_match[0])) { // 如果 $parm 看起来像Base64
        $decoded_param = base64_decode($last_result_match[0], true); // 严格模式解码
        if ($decoded_param !== false) {
            echo "解密并Base64解码后的数据:\n";
            echo $decoded_param;
        } else {
            echo "解密数据 ($parm 看似Base64但解码失败):\n";
            echo htmlspecialchars($parm);
        }
    } else { // 如果 $parm 不是Base64编码，或者格式不被识别
        echo "解密数据 (参数非Base64或格式未识别):\n";
        echo $parm;
    }
    echo "\n";
}

?>