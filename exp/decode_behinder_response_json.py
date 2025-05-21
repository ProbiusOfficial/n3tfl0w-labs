import json
import base64

def decode_behinder_response_json(json_string_input):
    """
    解码一个特定格式的JSON字符串，其中字符串类型的值可能是Base64编码的。
    此函数设计用于处理冰蝎（Behinder）Webshell响应中常见的JSON数据，
    这类数据通常将文件列表、命令输出等信息通过Base64编码后嵌入JSON结构中。

    输入的JSON字符串应为一个列表（list），列表中的每个元素是一个字典（dict）。
    函数会尝试解码字典中所有字符串类型的值。

    Args:
        json_string_input (str): 从冰蝎Webshell响应中获取的JSON字符串。
                                 例如: '[{"name":"Lg==","type":"ZGlyZWN0b3J5"}, ...]'

    Returns:
        list or None: 
            - 一个新的字典列表，其中Base64编码的字符串已被解码为UTF-8字符串。
              如果某个字符串无法被正确解码，其对应的值将是一个包含错误信息的字符串（例如："解码错误: ..."）。
            - 如果输入的 `json_string_input` 无法被解析为JSON，顶层JSON结构不是列表，
              或在处理过程中发生其他未预料的错误，则函数会打印错误信息到控制台并返回 None。
    """

    # 内部辅助函数：用于解码单个Base64编码的字符串
    def _decode_single_base64_string(encoded_str):
        """
        解码单个Base64编码的字符串。
        此函数会自动处理Base64编码所需的填充，并将解码后的字节串转换为UTF-8字符串。
        调用此函数前，外部逻辑应确保传入的是字符串类型。

        Args:
            encoded_str (str): Base64编码的字符串。

        Returns:
            str: 解码后的UTF-8字符串。如果解码过程中发生错误（如无效的Base64字符、错误的填充、非UTF-8内容），
                 则返回一个包含具体错误信息的字符串。
        """
        try:
            # Base64编码的字符串长度必须是4的倍数。如果不足，需要用'='进行填充。
            padding_needed = len(encoded_str) % 4
            if padding_needed:
                encoded_str += '=' * (4 - padding_needed)
            
            # 将Base64编码的字符串解码为字节串
            decoded_bytes = base64.b64decode(encoded_str)
            
            # 将解码后的字节串按UTF-8编码转换为字符串
            return decoded_bytes.decode('utf-8')
        except Exception as e:
            # 如果在解码过程中发生任何异常，则返回一个错误信息字符串。
            # 这与原始代码中处理解码错误的方式保持一致。
            return f"解码错误: {e}"

    try:
        # 1. 解析JSON字符串
        # 尝试将输入字符串解析为Python的JSON对象。
        try:
            parsed_json_data = json.loads(json_string_input)
        except json.JSONDecodeError as e:
            # 如果JSON字符串格式不正确，则无法解析。
            print(f"JSON解析错误: {e}")
            return None
        
        # 2. 验证顶层JSON结构是否为列表
        # 根据冰蝎响应的常见格式，预期顶层数据结构是一个列表。
        if not isinstance(parsed_json_data, list):
            print("错误：输入的数据不是一个JSON列表结构。")
            return None

        # 3. 遍历列表并解码字典中的字符串值
        # 用于存储最终解码结果的列表。
        decoded_result_list = []
        for item in parsed_json_data:
            # 列表中的每个项目通常是一个字典。
            if not isinstance(item, dict):
                # 如果某项目不是字典，则按原样保留，并打印警告。
                print(f"警告：列表中的项目不是字典类型：{item}")
                decoded_result_list.append(item) 
                continue

            # 为当前项目创建一个新的字典，用于存放解码后的键值对。
            decoded_item_dict = {}
            for key, value in item.items():
                # 只对字符串类型的值尝试进行Base64解码。
                if isinstance(value, str):
                    decoded_item_dict[key] = _decode_single_base64_string(value)
                else:
                    # 其他类型的值（如数字、布尔值、null或已是其他复杂结构）保持原样。
                    decoded_item_dict[key] = value
            decoded_result_list.append(decoded_item_dict)
        
        return decoded_result_list

    except TypeError as e: 
        #捕获 `json.loads` 可能因输入类型不正确（例如，传入None或数字而非字符串）引发的TypeError。
        print(f"JSON加载或处理时类型错误: {e}。请确保输入是字符串。")
        return None
    except Exception as e: 
        # 捕获在解析或处理过程中可能发生的任何其他未预料的错误，
        # 这与原始代码中的通用异常捕获逻辑一致。
        print(f"处理过程中发生未知错误: {e}")
        return None

# --- 以下是使用您提供的示例输入进行测试的部分 ---
if __name__ == '__main__':
    # 提供的输入 JSON 字符串
    input_json_example = '[{"name":"Lg==","size":"NDA5Ng==","lastModified":"MjAyMi0wNy0wNyAwMjoyNDo0NA==","perm":"Ui9XL0U=","type":"ZGlyZWN0b3J5"},{"name":"Li4=","size":"NDA5Ng==","lastModified":"MjAyMi0wNy0wNyAwMjowNToxOQ==","perm":"Ui8tL0U=","type":"ZGlyZWN0b3J5"},{"name":"bXlzcWw2NjYxMjMuYw==","size":"NzQ0NQ==","lastModified":"MjAyMi0wNy0wNyAwMjoyNDo0NA==","perm":"Ui9XLy0=","type":"ZmlsZQ=="},{"name":"bXlzcWxfcHJpdmVzY19leHBsb2l0","size":"NDA5Ng==","lastModified":"MjAyMi0wNy0wNiAxMjoyMTowMA==","perm":"Ui9XL0U=","type":"ZGlyZWN0b3J5"}]' # 缩短了示例以简洁

    print("原始JSON输入:")
    print(input_json_example)

    # 解码数据
    decoded_result = decode_behinder_response_json(input_json_example)

    # 打印解码后的结果
    if decoded_result:
        print("\n解码后的数据:")
        for entry in decoded_result:
            print(entry)
    else:
        print("\n数据解码失败或输入有误。")
    
    # 测试一个包含无效Base64的例子
    invalid_b64_json_example = '[{"name":"Invalid-B64!@#","description":"bm9ybWFsIHRleHQ="}]' # "normal text"
    print("\n测试包含无效Base64的JSON输入:")
    print(invalid_b64_json_example)
    decoded_invalid_result = decode_behinder_response_json(invalid_b64_json_example)
    if decoded_invalid_result:
        print("\n解码后的数据 (包含错误信息):")
        for entry in decoded_invalid_result:
            print(entry)

    # 测试非列表JSON输入
    invalid_json_format_example = '{"error":"this is not a list"}'
    print("\n测试非列表JSON输入:")
    print(invalid_json_format_example)
    decoded_invalid_format_result = decode_behinder_response_json(invalid_json_format_example)
    if decoded_invalid_format_result:
        print("\n解码后的数据:") # 实际上这里不会打印，因为会返回None
        for entry in decoded_invalid_format_result:
            print(entry)

    # 测试非JSON字符串输入
    not_a_json_string_example = "This is just a plain string, not JSON."
    print("\n测试非JSON字符串输入:")
    print(not_a_json_string_example)
    decoded_not_json = decode_behinder_response_json(not_a_json_string_example)
    if decoded_not_json:
         print("\n解码后的数据:")
         for entry in decoded_not_json:
            print(entry)