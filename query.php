<?php
 // 2024-05-23  本接口由api.vv1234.cn/ip/ 提供，纯公益服务，请不要恶意攻击本接口,如遇接口故障，请联系 vv1234.cn 

function get_ip_address($domain) {
    // 尝试获取 AAAA 记录 (IPv6)
    $records = dns_get_record($domain, DNS_AAAA);
    if ($records !== false && !empty($records)) {
        return $records[0]['ipv6'];
    }

    // 如果没有找到 IPv6 地址，尝试获取 A 记录 (IPv4)
    $ipv4 = gethostbyname($domain);
    if (filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $ipv4;
    }

    return null;
}

if (isset($_GET['input'])) {
    $input = $_GET['input'];
    $ip = null;

    // 检查输入是否为有效的 IP 地址
    if (filter_var($input, FILTER_VALIDATE_IP)) {
        $ip = $input;
    } else {
        // 尝试将域名解析为 IP 地址
        $ip = get_ip_address($input);
        if ($ip === null) {
            echo "域名无法解析";
            exit;
        }
    }

    // 构建 API 请求 URL
    $api_url = "https://api.vv1234.cn/ip/api.php?ip_url=$ip&from=ip.vv1234.cn&token=test&code=json";

    // 使用 cURL 发送请求并获取响应
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 检查 HTTP 状态码
    if ($httpcode != 200) {
        echo "API 请求失败，HTTP 状态码: $httpcode";
        exit;
    }

// 清理 API 响应内容，去除控制字符
$clean_response = preg_replace('/[\x00-\x1F]/', '', $response);


    // 解析 JSON 响应
  $data = json_decode($clean_response, true);
 // $data = json_decode($response, true);

//var_dump($data);
     if (isset($data['ip'])) {
        echo "IP 地址: " . htmlspecialchars($data['ip']) . "<br>";
        echo "城市: " . htmlspecialchars($data['city']) . "<br>";
        echo "ASN: " . htmlspecialchars($data['asn']) . "<br>";
    } else {
        echo "查询失败，响应数据: " . htmlspecialchars($response);
    }
    
 
} else {
    echo "请输入 IP 地址或域名";
}
?>
