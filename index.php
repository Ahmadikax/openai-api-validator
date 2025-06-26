<?php
/**
 * OpenAI API Key Validator
 * 
 * أداة بسيطة للتحقق من صحة مفتاح API الخاص بـ OpenAI
 */

// تعريف المتغيرات
$apiKey = '';
$result = '';
$status = '';
$message = '';
$details = '';

// التحقق مما إذا تم إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // الحصول على مفتاح API من النموذج
    $apiKey = isset($_POST['api_key']) ? trim($_POST['api_key']) : '';
    
    // التحقق من أن المفتاح غير فارغ
    if (empty($apiKey)) {
        $status = 'error';
        $message = 'الرجاء إدخال مفتاح API';
    } else {
        // التحقق من صحة مفتاح API
        $validationResult = validateOpenAIKey($apiKey);
        $status = $validationResult['status'];
        $message = $validationResult['message'];
        $details = $validationResult['details'];
    }
}

/**
 * التحقق من صحة مفتاح API الخاص بـ OpenAI
 * 
 * @param string $apiKey مفتاح API المراد التحقق منه
 * @return array نتيجة التحقق
 */
function validateOpenAIKey($apiKey) {
    // تهيئة المصفوفة للنتيجة
    $result = [
        'status' => 'error',
        'message' => '',
        'details' => ''
    ];
    
    // إعداد طلب cURL
    $ch = curl_init();
    
    // تعيين URL للطلب (استخدام نقطة نهاية بسيطة للتحقق)
    curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/models');
    
    // تعيين الرؤوس
    $headers = [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // تعيين خيارات أخرى
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    // تنفيذ الطلب
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    // إغلاق جلسة cURL
    curl_close($ch);
    
    // معالجة الاستجابة
    if ($curlError) {
        $result['message'] = 'خطأ في الاتصال: ' . $curlError;
    } else {
        $responseData = json_decode($response, true);
        
        // التحقق من رمز الاستجابة
        if ($httpCode === 200) {
            $result['status'] = 'success';
            $result['message'] = 'مفتاح API صالح!';
            
            // إضافة معلومات إضافية عن النماذج المتاحة
            if (isset($responseData['data']) && is_array($responseData['data'])) {
                $modelCount = count($responseData['data']);
                $result['details'] = 'تم العثور على ' . $modelCount . ' نموذج متاح.';
            }
        } else {
            // استخراج رسالة الخطأ من الاستجابة
            $errorMessage = isset($responseData['error']['message']) 
                ? $responseData['error']['message'] 
                : 'خطأ غير معروف (رمز الاستجابة: ' . $httpCode . ')';
            
            $result['message'] = 'مفتاح API غير صالح: ' . $errorMessage;
            
            // تحديد نوع الخطأ بشكل أكثر تحديدًا
            if ($httpCode === 401) {
                $result['details'] = 'خطأ في المصادقة. تأكد من أن المفتاح صحيح وغير منتهي الصلاحية.';
            } elseif ($httpCode === 429) {
                $result['details'] = 'تم تجاوز حد الطلبات. انتظر قليلاً وحاول مرة أخرى.';
            } elseif ($httpCode >= 500) {
                $result['details'] = 'خطأ في خادم OpenAI. حاول مرة أخرى لاحقًا.';
            }
        }
    }
    
    return $result;
}

// تحديد اللغة والاتجاه للصفحة
$dir = 'rtl';
$lang = 'ar';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أداة التحقق من مفتاح API الخاص بـ OpenAI</title>
    <style>
        :root {
            --primary-color: #10a37f;
            --error-color: #e53e3e;
            --success-color: #38a169;
            --bg-color: #f7f7f7;
            --card-bg: #ffffff;
            --text-color: #333333;
            --border-color: #e2e8f0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            padding: 20px;
            margin: 0;
            direction: rtl;
        }
        
        .container {
            max-width: 800px;
            margin: 40px auto;
            background-color: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        
        input[type="text"]:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(16, 163, 127, 0.2);
        }
        
        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s;
            width: 100%;
        }
        
        button:hover {
            background-color: #0c8b6c;
        }
        
        .result {
            margin-top: 30px;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid var(--border-color);
        }
        
        .result.success {
            background-color: rgba(56, 161, 105, 0.1);
            border-color: var(--success-color);
        }
        
        .result.error {
            background-color: rgba(229, 62, 62, 0.1);
            border-color: var(--error-color);
        }
        
        .result-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-color);
        }
        
        .result-message {
            margin-bottom: 15px;
        }
        
        .result-details {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: #666;
        }
        
        .api-key-info {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-right: 3px solid var(--primary-color);
        }
        
        .language-switch {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .language-switch a {
            color: var(--primary-color);
            text-decoration: none;
            margin: 0 10px;
        }
        
        .language-switch a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 20px;
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="language-switch">
            <a href="index.php">العربية</a> | <a href="index_en.php">English</a>
        </div>
        
        <h1>أداة التحقق من مفتاح API الخاص بـ OpenAI</h1>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="api_key">مفتاح API الخاص بـ OpenAI:</label>
                <input 
                    type="text" 
                    id="api_key" 
                    name="api_key" 
                    placeholder="أدخل مفتاح API الخاص بك هنا (يبدأ بـ sk-...)" 
                    value="<?php echo htmlspecialchars($apiKey); ?>" 
                    required
                >
                <div class="api-key-info">
                    <strong>ملاحظة:</strong> مفتاح API الخاص بك آمن ولا يتم تخزينه على الخادم. يتم استخدامه فقط للتحقق من صحته مع OpenAI.
                </div>
            </div>
            
            <button type="submit">التحقق من المفتاح</button>
        </form>
        
        <?php if (!empty($status)): ?>
            <div class="result <?php echo $status; ?>">
                <div class="result-title">
                    <?php if ($status === 'success'): ?>
                        ✅ نجاح
                    <?php else: ?>
                        ❌ خطأ
                    <?php endif; ?>
                </div>
                <div class="result-message"><?php echo htmlspecialchars($message); ?></div>
                <?php if (!empty($details)): ?>
                    <div class="result-details"><?php echo htmlspecialchars($details); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="footer">
            <p>هذه الأداة تساعدك في التحقق من صحة مفتاح API الخاص بـ OpenAI.</p>
            <p>لا يتم تخزين مفاتيح API أو مشاركتها مع أي طرف ثالث.</p>
        </div>
    </div>
</body>
</html>