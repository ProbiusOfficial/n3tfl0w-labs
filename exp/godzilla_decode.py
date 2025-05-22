import base64
import zlib


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
    response_data = ''
    key = '421eb7f1b8e4b3cf'

    decoded_content = decode_godzilla_request(response_data, key)

    if decoded_content:
        print(decoded_content)
    else:
        print("Failed to decode the Godzilla response.")