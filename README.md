# أداة التحقق من مفتاح API الخاص بـ OpenAI

أداة بسيطة للتحقق من صحة مفتاح API الخاص بـ OpenAI. تساعدك هذه الأداة على التأكد من أن مفتاح API الخاص بك يعمل بشكل صحيح قبل استخدامه في تطبيقاتك.

## المميزات

- واجهة مستخدم بسيطة وسهلة الاستخدام
- تحقق فوري من صحة مفتاح API
- عرض رسائل خطأ مفصلة في حالة وجود مشكلة
- عرض معلومات إضافية عن النماذج المتاحة في حالة نجاح التحقق
- تصميم متجاوب يعمل على جميع الأجهزة
- دعم كامل للغة العربية والاتجاه من اليمين إلى اليسار (RTL)

## متطلبات التشغيل

- خادم ويب مع دعم PHP (الإصدار 7.0 أو أحدث)
- تمكين امتداد cURL في PHP
- اتصال بالإنترنت للوصول إلى واجهة برمجة تطبيقات OpenAI

## كيفية الاستخدام

1. قم بتحميل الملفات إلى خادم الويب الخاص بك
2. افتح المتصفح وانتقل إلى عنوان URL للأداة
3. أدخل مفتاح API الخاص بـ OpenAI في الحقل المخصص
4. انقر على زر "التحقق من المفتاح"
5. ستظهر نتيجة التحقق مباشرة على الصفحة

## الأمان

- لا يتم تخزين مفاتيح API على الخادم
- يتم استخدام المفتاح فقط للتحقق من صحته مع OpenAI
- لا يتم مشاركة المفتاح مع أي طرف ثالث

## كيفية الحصول على مفتاح API من OpenAI

1. قم بإنشاء حساب على [موقع OpenAI](https://platform.openai.com/)
2. انتقل إلى صفحة [API Keys](https://platform.openai.com/api-keys)
3. انقر على زر "Create new secret key"
4. قم بتسمية المفتاح واحفظه في مكان آمن

## استكشاف الأخطاء وإصلاحها

إذا واجهت مشكلة في التحقق من المفتاح، تحقق من:

- أن المفتاح تم نسخه بشكل صحيح (يبدأ عادة بـ `sk-`)
- أن حسابك في OpenAI نشط وليس هناك مشكلة في الفوترة
- أن لديك رصيد كافٍ في حسابك (إذا كنت تستخدم الإصدار المدفوع)
- أن اتصالك بالإنترنت يعمل بشكل صحيح

## الترخيص

هذه الأداة متاحة للاستخدام المجاني. يمكنك تعديلها وتوزيعها حسب احتياجاتك.