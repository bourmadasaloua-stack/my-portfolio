<?php
// Simple read of inquiries.json
$file = 'inquiries.json';
$inquiries = [];

if (file_exists($file)) {
    $content = file_get_contents($file);
    $inquiries = json_decode($content, true);
    if (!is_array($inquiries)) {
        $inquiries = [];
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $inquiries = array_filter($inquiries, function($item) use ($id) {
        return $item['id'] !== $id;
    });
    file_put_contents($file, json_encode(array_values($inquiries), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header('Location: inquiries.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة طلبات المشاريع والاستفسارات</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        h1 i {
            color: #0891b2;
        }
        .btn-back {
            background-color: #f1f5f9;
            color: #475569;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .btn-back:hover {
            background-color: #e2e8f0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 15px;
            text-align: right;
            border-bottom: 1px solid #f1f5f9;
        }
        th {
            background-color: #f8fafc;
            font-weight: 700;
            color: #475569;
        }
        tr:hover {
            background-color: #fafafa;
        }
        .badge {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .badge-budget { background: rgba(8, 145, 178, 0.08); color: #0891b2; }
        .badge-summarizer { background: rgba(168, 85, 247, 0.08); color: #a855f7; }
        .badge-smart { background: rgba(79, 70, 229, 0.08); color: #4f46e5; }
        .badge-custom { background: rgba(245, 158, 11, 0.08); color: #d97706; }
        .btn-delete {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.1);
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s;
            white-space: nowrap;
        }
        .btn-delete:hover {
            background: #ef4444;
            color: white;
        }
        .no-data {
            text-align: center;
            padding: 50px 20px;
            color: #64748b;
            font-size: 1.1rem;
        }
        .no-data i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 15px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fa-solid fa-inbox"></i> صندوق استفسارات المشاريع</h1>
            <a href="index.html" class="btn-back"><i class="fa-solid fa-arrow-right"></i> العودة للمعرض</a>
        </header>

        <?php if (empty($inquiries)): ?>
            <div class="no-data">
                <i class="fa-solid fa-folder-open"></i>
                لا توجد رسائل أو استفسارات حالياً.
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>الاسم الكامل</th>
                            <th>البريد الإلكتروني</th>
                            <th>رقم الهاتف</th>
                            <th>المشروع المهتم به</th>
                            <th>تفاصيل الاستفسار</th>
                            <th>التاريخ والوقت</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($inquiries) as $inquiry): 
                            $badgeClass = 'badge-custom';
                            if ($inquiry['project'] === 'نظام ميزانية التسيير') $badgeClass = 'badge-budget';
                            elseif ($inquiry['project'] === 'CourseCraft AI') $badgeClass = 'badge-summarizer';
                            elseif ($inquiry['project'] === 'BASMA SMART') $badgeClass = 'badge-smart';
                        ?>
                            <tr>
                                <td style="font-weight: 700;"><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($inquiry['email']); ?>"><?php echo htmlspecialchars($inquiry['email']); ?></a></td>
                                <td><?php echo htmlspecialchars($inquiry['phone'] ?: 'غير متوفر'); ?></td>
                                <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($inquiry['project']); ?></span></td>
                                <td style="max-width: 350px; font-size: 0.9rem; line-height: 1.5;"><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></td>
                                <td style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($inquiry['date']); ?></td>
                                <td>
                                    <a href="inquiries.php?action=delete&id=<?php echo urlencode($inquiry['id']); ?>" class="btn-delete" onclick="return confirm('هل أنت متأكد من حذف هذا الاستفسار؟')"><i class="fa-solid fa-trash"></i> حذف</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
