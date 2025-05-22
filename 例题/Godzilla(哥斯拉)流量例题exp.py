
import os
import zlib
import base64
import requests

# 3.x-4.x Godzilla PHP Webshell PHP-EVAL-XOR-BASE64

def decode_godzilla_request(encrypted_data: str, key: str) -> str | None:
    def _xor_decode(data_bytes: bytes, decode_key: str) -> bytes:
        decoded_bytes = bytearray(data_bytes)
        key_bytes = bytearray(decode_key.encode('utf-8'))
        for i in range(len(decoded_bytes)):
            c = key_bytes[(i + 1) & 15]
            decoded_bytes[i] ^= c
        return decoded_bytes

    try:
        base64_decoded = base64.b64decode(encrypted_data)
        xor_decoded_bytes = _xor_decode(base64_decoded, key)
        return xor_decoded_bytes.decode('utf-8', errors='ignore')
    except Exception:
        return None
    
def decode_godzilla_response(encrypted_response_data: str, key: str) -> str | None:
    def _xor_decode(data_bytes: bytes, decode_key: str) -> bytes:
        decoded_bytes = bytearray(data_bytes)
        key_bytes = bytearray(decode_key.encode('utf-8'))
        for i in range(len(decoded_bytes)):
            c = key_bytes[(i + 1) & 15]
            decoded_bytes[i] ^= c
        return decoded_bytes

    try:
        base64_decoded = base64.b64decode(encrypted_response_data)
        xor_decoded_bytes = _xor_decode(base64_decoded, key)
        decompressed_data = zlib.decompress(xor_decoded_bytes, 16 + zlib.MAX_WBITS)
        return decompressed_data.decode('utf-8', errors='ignore')
    except Exception:
        return None

if __name__ == "__main__":
    
    passwd = 'ctfsogood'
    key_passwd = 'babyshell'
    key = '421eb7f1b8e4b3cf'

    current_dir = os.path.dirname(os.path.abspath(__file__))
    print(f'[+] Current directory: {os.listdir(current_dir)}')

    for filename in os.listdir(current_dir):
        file_path = os.path.join(current_dir, filename)
        file_content = open(file_path, 'r', encoding='utf-8').read()
        # URL decode
        file_content = requests.utils.unquote(file_content)
        print(f'[+] Processing file: {filename}')
        if passwd in file_content:
            try:
                request_content = file_content.split(f'{key_passwd}=')[1].split(';')[0]
            except IndexError:
                print(f'[!] 无法在文件内容中找到 ${key_passwd}= 字符串')
                continue
            print(f'[+] Found request content: {request_content}')
            print(f'解密后: {decode_godzilla_request(request_content, key)}')
        else:
            response_content = file_content[16:-16]
            print(f'[+] Found response content: {response_content}')
            print(f'解密后: {decode_godzilla_response(response_content, key)}')


