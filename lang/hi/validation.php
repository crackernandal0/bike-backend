<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */


    'accepted' => ':attribute को स्वीकार किया जाना चाहिए।',
    'accepted_if' => ':attribute को स्वीकार किया जाना चाहिए जब :other :value हो।',
    'active_url' => ':attribute एक मान्य URL होना चाहिए।',
    'after' => ':attribute एक तारीख होनी चाहिए जो :date के बाद हो।',
    'after_or_equal' => ':attribute :date के बाद या उसके बराबर की तारीख होनी चाहिए।',
    'alpha' => ':attribute में केवल अक्षर होने चाहिए।',
    'alpha_dash' => ':attribute में केवल अक्षर, संख्याएं, डैश और अंडरस्कोर होने चाहिए।',
    'alpha_num' => ':attribute में केवल अक्षर और संख्याएं होनी चाहिए।',
    'array' => ':attribute एक सरणी होनी चाहिए।',
    'ascii' => ':attribute में केवल सिंगल-बाइट अल्फान्यूमेरिक कैरेक्टर और सिंबल्स होने चाहिए।',
    'before' => ':attribute :date से पहले की तारीख होनी चाहिए।',
    'before_or_equal' => ':attribute :date से पहले या उसके बराबर की तारीख होनी चाहिए।',
    'between' => [
        'array' => ':attribute में :min और :max आइटम्स के बीच होना चाहिए।',
        'file' => ':attribute :min और :max किलोबाइट्स के बीच होना चाहिए।',
        'numeric' => ':attribute :min और :max के बीच होना चाहिए।',
        'string' => ':attribute :min और :max वर्णों के बीच होना चाहिए।',
    ],
    'boolean' => ':attribute सही या गलत होना चाहिए।',
    'can' => ':attribute में एक अप्राधिकृत मान शामिल है।',
    'confirmed' => ':attribute पुष्टि मेल नहीं खाती।',
    'contains' => ':attribute में एक आवश्यक मान नहीं है।',
    'current_password' => 'पासवर्ड गलत है।',
    'date' => ':attribute एक मान्य तारीख होनी चाहिए।',
    'date_equals' => ':attribute :date के बराबर की तारीख होनी चाहिए।',
    'date_format' => ':attribute :format प्रारूप से मेल खानी चाहिए।',
    'decimal' => ':attribute में :decimal दशमलव स्थान होने चाहिए।',
    'declined' => ':attribute को अस्वीकार किया जाना चाहिए।',
    'declined_if' => ':attribute को अस्वीकार किया जाना चाहिए जब :other :value हो।',
    'different' => ':attribute और :other अलग होना चाहिए।',
    'digits' => ':attribute :digits अंकों का होना चाहिए।',
    'digits_between' => ':attribute :min और :max अंकों के बीच होना चाहिए।',
    'dimensions' => ':attribute का चित्र आयाम अमान्य है।',
    'distinct' => ':attribute में डुप्लिकेट मान है।',
    'doesnt_end_with' => ':attribute निम्नलिखित में से किसी एक के साथ समाप्त नहीं होना चाहिए: :values।',
    'doesnt_start_with' => ':attribute निम्नलिखित में से किसी एक के साथ शुरू नहीं होना चाहिए: :values।',
    'email' => ':attribute एक मान्य ईमेल पता होना चाहिए।',
    'ends_with' => ':attribute निम्नलिखित में से किसी एक के साथ समाप्त होना चाहिए: :values।',
    'enum' => 'चयनित :attribute अमान्य है।',
    'exists' => 'चयनित :attribute अमान्य है।',
    'extensions' => ':attribute में निम्नलिखित एक्सटेंशन में से एक होना चाहिए: :values।',
    'file' => ':attribute एक फाइल होनी चाहिए।',
    'filled' => ':attribute में एक मान होना चाहिए।',
    'gt' => [
        'array' => ':attribute में :value आइटम्स से अधिक होना चाहिए।',
        'file' => ':attribute :value किलोबाइट्स से अधिक होना चाहिए।',
        'numeric' => ':attribute :value से अधिक होना चाहिए।',
        'string' => ':attribute :value वर्णों से अधिक होना चाहिए।',
    ],
    'gte' => [
        'array' => ':attribute में कम से कम :value आइटम्स होने चाहिए।',
        'file' => ':attribute :value किलोबाइट्स या अधिक होना चाहिए।',
        'numeric' => ':attribute :value या अधिक होना चाहिए।',
        'string' => ':attribute :value वर्णों या अधिक होना चाहिए।',
    ],
    'hex_color' => ':attribute एक मान्य हेक्साडेसिमल रंग होना चाहिए।',
    'image' => ':attribute एक चित्र होना चाहिए।',
    'in' => 'चयनित :attribute अमान्य है।',
    'in_array' => ':attribute :other में मौजूद होना चाहिए।',
    'integer' => ':attribute एक पूर्णांक होना चाहिए।',
    'ip' => ':attribute एक मान्य IP पता होना चाहिए।',
    'ipv4' => ':attribute एक मान्य IPv4 पता होना चाहिए।',
    'ipv6' => ':attribute एक मान्य IPv6 पता होना चाहिए।',
    'json' => ':attribute एक मान्य JSON स्ट्रिंग होनी चाहिए।',
    'list' => ':attribute एक सूची होनी चाहिए।',
    'lowercase' => ':attribute लोअरकेस में होना चाहिए।',
    'lt' => [
        'array' => ':attribute में :value आइटम्स से कम होना चाहिए।',
        'file' => ':attribute :value किलोबाइट्स से कम होना चाहिए।',
        'numeric' => ':attribute :value से कम होना चाहिए।',
        'string' => ':attribute :value वर्णों से कम होना चाहिए।',
    ],
    'lte' => [
        'array' => ':attribute में :value आइटम्स से अधिक नहीं होना चाहिए।',
        'file' => ':attribute :value किलोबाइट्स या उससे कम होना चाहिए।',
        'numeric' => ':attribute :value या उससे कम होना चाहिए।',
        'string' => ':attribute :value वर्णों या उससे कम होना चाहिए।',
    ],
    'mac_address' => ':attribute एक मान्य MAC पता होना चाहिए।',
    'max' => [
        'array' => ':attribute में :max आइटम्स से अधिक नहीं होना चाहिए।',
        'file' => ':attribute :max किलोबाइट्स से अधिक नहीं होना चाहिए।',
        'numeric' => ':attribute :max से अधिक नहीं होना चाहिए।',
        'string' => ':attribute :max वर्णों से अधिक नहीं होना चाहिए।',
    ],
    'max_digits' => ':attribute में :max अंकों से अधिक नहीं होना चाहिए।',
    'mimes' => ':attribute :values प्रकार की फाइल होनी चाहिए।',
    'mimetypes' => ':attribute :values प्रकार की फाइल होनी चाहिए।',
    'min' => [
        'array' => ':attribute में कम से कम :min आइटम्स होने चाहिए।',
        'file' => ':attribute कम से कम :min किलोबाइट्स का होना चाहिए।',
        'numeric' => ':attribute कम से कम :min होना चाहिए।',
        'string' => ':attribute कम से कम :min वर्णों का होना चाहिए।',
    ],
    'min_digits' => ':attribute में कम से कम :min अंक होने चाहिए।',
    'missing' => ':attribute अनुपस्थित होना चाहिए।',
    'missing_if' => ':attribute अनुपस्थित होना चाहिए जब :other :value हो।',
    'missing_unless' => ':attribute अनुपस्थित होना चाहिए जब तक :other :value न हो।',
    'missing_with' => ':attribute अनुपस्थित होना चाहिए जब :values मौजूद हो।',
    'missing_with_all' => ':attribute अनुपस्थित होना चाहिए जब :values मौजूद हों।',
    'multiple_of' => ':attribute :value का गुणज होना चाहिए।',
    'not_in' => 'चयनित :attribute अमान्य है।',
    'not_regex' => ':attribute प्रारूप अमान्य है।',
    'numeric' => ':attribute एक संख्या होनी चाहिए।',
    'password' => [
        'letters' => ':attribute में कम से कम एक अक्षर होना चाहिए।',
        'mixed' => ':attribute में कम से कम एक अपरकेस और एक लोअरकेस अक्षर होना चाहिए।',
        'numbers' => ':attribute में कम से कम एक संख्या होनी चाहिए।',
        'symbols' => ':attribute में कम से कम एक प्रतीक होना चाहिए।',
        'uncompromised' => 'दिया गया :attribute एक डेटा लीक में पाया गया है। कृपया एक अलग :attribute चुनें।',
    ],
    'present' => ':attribute उपस्थित होना चाहिए।',
    'present_if' => ':attribute उपस्थित होना चाहिए जब :other :value हो।',
    'present_unless' => ':attribute उपस्थित होना चाहिए जब तक :other :value न हो।',
    'present_with' => ':attribute उपस्थित होना चाहिए जब :values उपस्थित हो।',
    'present_with_all' => ':attribute उपस्थित होना चाहिए जब :values उपस्थित हों।',
    'prohibited' => ':attribute निषिद्ध है।',
    'prohibited_if' => ':attribute निषिद्ध है जब :other :value हो।',
    'prohibited_unless' => ':attribute निषिद्ध है जब तक :other :value न हो।',
    'prohibits' => ':attribute :other को उपस्थित होने से निषिद्ध करता है।',
    'regex' => ':attribute प्रारूप अमान्य है।',
    'required' => ':attribute आवश्यक है।',
    'required_array_keys' => ':attribute में :values के लिए प्रविष्टियाँ होनी चाहिए।',
    'required_if' => ':attribute आवश्यक है जब :other :value हो।',
    'required_unless' => ':attribute आवश्यक है जब तक :other :values में न हो।',
    'required_with' => ':attribute आवश्यक है जब :values उपस्थित हो।',
    'required_with_all' => ':attribute आवश्यक है जब :values उपस्थित हों।',
    'required_without' => ':attribute आवश्यक है जब :values अनुपस्थित हो।',
    'required_without_all' => ':attribute आवश्यक है जब :values में से कोई भी अनुपस्थित हों।',
    'same' => ':attribute और :other मेल खाने चाहिए।',
    'size' => [
        'array' => ':attribute में :size आइटम्स होने चाहिए।',
        'file' => ':attribute :size किलोबाइट्स का होना चाहिए।',
        'numeric' => ':attribute :size का होना चाहिए।',
        'string' => ':attribute :size वर्णों का होना चाहिए।',
    ],
    'starts_with' => ':attribute निम्नलिखित में से किसी एक के साथ शुरू होना चाहिए: :values।',
    'string' => ':attribute एक स्ट्रिंग होनी चाहिए।',
    'timezone' => ':attribute एक मान्य समय क्षेत्र होना चाहिए।',
    'unique' => ':attribute पहले से लिया जा चुका है।',
    'uploaded' => ':attribute अपलोड करने में विफल।',
    'uppercase' => ':attribute अपरकेस में होना चाहिए।',
    'url' => ':attribute एक मान्य URL होना चाहिए।',
    'ulid' => ':attribute एक मान्य ULID होना चाहिए।',
    'uuid' => ':attribute एक मान्य UUID होना चाहिए।',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */


    'attributes' => [],

];
