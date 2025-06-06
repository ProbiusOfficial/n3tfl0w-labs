import gzip
import hashlib
import struct

# 3.x-4.x Godzilla PHP Webshell PHP_XOR_RAW

def gen_key(key):
    return hashlib.md5(key.encode("utf-8")).hexdigest()[:16]

def decode_raw(bytes_data, key):
    return struct.pack('B' * len(bytes_data), *(cs_byte ^ ord(key[i + 1 & 15]) for i, cs_byte in enumerate(bytes_data)))

def gzip_decompress(byte_data):
    if not byte_data:
        return byte_data
    try:
        return gzip.decompress(byte_data)
    except OSError:
        return byte_data

def decrypt_php_xor_raw(response_content: bytes, shell_key: str, output_filename: str = None):
    if not isinstance(response_content, bytes):
        raise TypeError("response_content 必须是字节类型 (bytes)")
    if not isinstance(shell_key, str):
        raise TypeError("shell_key 必须是字符串类型")

    secret_key = gen_key(shell_key)
    decoded_bytes_content = gzip_decompress(decode_raw(response_content, secret_key))

    if output_filename:
        try:
            with open(output_filename, 'wb') as f:
                f.write(decoded_bytes_content)
            print(f"解码后的二进制内容已保存到文件: '{output_filename}'")
        except IOError as e:
            print(f"保存文件 '{output_filename}' 时出错: {e}")

    return decoded_bytes_content

if __name__ == "__main__":
    hex_string_from_wireshark = ""

    try:
        sample_php_xor_raw_response = bytes.fromhex(hex_string_from_wireshark)
    except ValueError as e:
        print(f"错误: 转换十六进制字符串失败。请检查您的输入，确保只包含有效的十六进制字符且没有空格或换行符。错误信息: {e}")
        exit()

    shell_key = "key"
    output_file = "decoded_image.png"

    print(f"尝试使用密钥: '{shell_key}' 解密并将内容保存为二进制文件...")

    try:
        decoded_bytes_result = decrypt_php_xor_raw(
            response_content=sample_php_xor_raw_response,
            shell_key=shell_key,
            output_filename=output_file
        )
        print("\n--- 解密完成 ---")
        print(f"内容已保存为二进制文件: '{output_file}'")
        print(f"文件前16字节: {decoded_bytes_result[:16].hex()}")
    except Exception as e:
        print(f"解密过程中发生错误: {e}")