<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$name = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : '';
$phone = isset($_POST['phone']) ? trim(htmlspecialchars($_POST['phone'])) : '';
$project = isset($_POST['project']) ? trim(htmlspecialchars($_POST['project'])) : '';
$message = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'])) : '';

if (empty($name) || empty($email) || empty($project) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields']);
    exit;
}

$file = 'inquiries.json';
$inquiries = [];

if (file_exists($file)) {
    $content = file_get_contents($file);
    $inquiries = json_decode($content, true);
    if (!is_array($inquiries)) {
        $inquiries = [];
    }
}

$newInquiry = [
    'id' => uniqid(),
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'project' => $project,
    'message' => $message,
    'date' => date('Y-m-d H:i:s')
];

$inquiries[] = $newInquiry;

if (file_put_contents($file, json_encode($inquiries, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    // Send Email Notification
    $to = "bourmadasaloua@gmail.com";
    $subject = "=?UTF-8?B?".base64_encode("[طلب مشروع جديد] استفسار من معرض أعمالك")."?=";

    $emailBody = "
    <html>
    <head>
        <title>طلب مشروع جديد</title>
    </head>
    <body dir='rtl' style='font-family: Arial, sans-serif; background-color: #f8fafc; padding: 20px; color: #0f172a;'>
        <div style='max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);'>
            <h2 style='color: #0891b2; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 20px; text-align: right;'>طلب مشروع جديد من معرض الأعمال</h2>
            <table style='width: 100%; border-collapse: collapse; text-align: right;'>
                <tr style='background: #f8fafc;'>
                    <td style='padding: 10px; font-weight: bold; width: 150px;'>اسم الزبون:</td>
                    <td style='padding: 10px;'>$name</td>
                </tr>
                <tr>
                    <td style='padding: 10px; font-weight: bold;'>البريد الإلكتروني:</td>
                    <td style='padding: 10px;'><a href='mailto:$email'>$email</a></td>
                </tr>
                <tr style='background: #f8fafc;'>
                    <td style='padding: 10px; font-weight: bold;'>رقم الهاتف:</td>
                    <td style='padding: 10px;'>".($phone ?: 'غير متوفر')."</td>
                </tr>
                <tr>
                    <td style='padding: 10px; font-weight: bold;'>المشروع المختار:</td>
                    <td style='padding: 10px; color: #4f46e5; font-weight: bold;'>$project</td>
                </tr>
                <tr style='background: #f8fafc;'>
                    <td style='padding: 10px; font-weight: bold;'>تاريخ الطلب:</td>
                    <td style='padding: 10px; color: #64748b;'>".date('Y-m-d H:i:s')."</td>
                </tr>
            </table>
            <div style='margin-top: 25px; text-align: right;'>
                <h4 style='color: #0f172a; margin-bottom: 10px;'>تفاصيل الرسالة:</h4>
                <div style='background: #f1f5f9; padding: 15px; border-radius: 8px; line-height: 1.6; color: #334155;'>
                    ".nl2br($message)."
                </div>
            </div>
            <div style='margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 15px; text-align: center; font-size: 0.85rem; color: #94a3b8;'>
                هذه الرسالة مرسلة تلقائياً من خادم موقع معرض أعمالك.
            </div>
        </div>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: <noreply@bourmadasaloua.com>" . "\r\n";

    @mail($to, $subject, $emailBody, $headers);

    echo json_encode(['status' => 'success', 'message' => 'Inquiry saved successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save inquiry. Please check write permissions.']);
}
?>
