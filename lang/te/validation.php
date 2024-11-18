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

    'accepted' => ':attribute ఫీల్డ్ ఆమోదించబడాలి.',
    'accepted_if' => ':attribute ఫీల్డ్ :other :value ఉన్నప్పుడు ఆమోదించబడాలి.',
    'active_url' => ':attribute ఫీల్డ్ సరైన URL అయి ఉండాలి.',
    'after' => ':attribute ఫీల్డ్ :date తర్వాత తేదీ అయి ఉండాలి.',
    'after_or_equal' => ':attribute ఫీల్డ్ :date తర్వాత లేదా సమానమైన తేదీ అయి ఉండాలి.',
    'alpha' => ':attribute ఫీల్డ్ కేవలం అక్షరాలను మాత్రమే కలిగి ఉండాలి.',
    'alpha_dash' => ':attribute ఫీల్డ్ కేవలం అక్షరాలు, సంఖ్యలు, హైఫెన్‌లు మరియు అండర్‌స్కోర్‌లను మాత్రమే కలిగి ఉండాలి.',
    'alpha_num' => ':attribute ఫీల్డ్ కేవలం అక్షరాలు మరియు సంఖ్యలను మాత్రమే కలిగి ఉండాలి.',
    'array' => ':attribute ఫీల్డ్ ఒక అరికే అయి ఉండాలి.',
    'ascii' => ':attribute ఫీల్డ్ కేవలం ఒక బైట్ అక్షరాలు మరియు చిహ్నాలను మాత్రమే కలిగి ఉండాలి.',
    'before' => ':attribute ఫీల్డ్ :date ముందటి తేదీ అయి ఉండాలి.',
    'before_or_equal' => ':attribute ఫీల్డ్ :date ముందు లేదా సమానమైన తేదీ అయి ఉండాలి.',
    'between' => [
        'array' => ':attribute ఫీల్డ్ :min మరియు :max అంశాలు కలిగి ఉండాలి.',
        'file' => ':attribute ఫీల్డ్ :min మరియు :max కిలోబైట్లు మధ్య ఉండాలి.',
        'numeric' => ':attribute ఫీల్డ్ :min మరియు :max మధ్య ఉండాలి.',
        'string' => ':attribute ఫీల్డ్ :min మరియు :max అక్షరాలు మధ్య ఉండాలి.',
    ],
    'boolean' => ':attribute ఫీల్డ్ నిజం లేదా తప్పు అయి ఉండాలి.',
    'can' => ':attribute ఫీల్డ్ అనుమతించని విలువను కలిగి ఉంది.',
    'confirmed' => ':attribute ఫీల్డ్ నిర్ధారణ సరిపోదు.',
    'contains' => ':attribute ఫీల్డ్ అవసరమైన విలువను కలిగి లేదు.',
    'current_password' => 'పాస్వర్డ్ తప్పు.',
    'date' => ':attribute ఫీల్డ్ ఒక సరైన తేదీ అయి ఉండాలి.',
    'date_equals' => ':attribute ఫీల్డ్ :date కు సమానమైన తేదీ అయి ఉండాలి.',
    'date_format' => ':attribute ఫీల్డ్ :format పద్ధతిని అనుసరించాలి.',
    'decimal' => ':attribute ఫీల్డ్ :decimal దశాంశ స్థానాలను కలిగి ఉండాలి.',
    'declined' => ':attribute ఫీల్డ్ తిరస్కరించబడాలి.',
    'declined_if' => ':attribute ఫీల్డ్ :other :value ఉన్నప్పుడు తిరస్కరించబడాలి.',
    'different' => ':attribute ఫీల్డ్ మరియు :other వేరుగా ఉండాలి.',
    'digits' => ':attribute ఫీల్డ్ :digits అంకెలను కలిగి ఉండాలి.',
    'digits_between' => ':attribute ఫీల్డ్ :min మరియు :max అంకెలు మధ్య ఉండాలి.',
    'dimensions' => ':attribute ఫీల్డ్ అమాన్య చిత్రం కొలతలను కలిగి ఉంది.',
    'distinct' => ':attribute ఫీల్డ్ డుప్లికేట్ విలువను కలిగి ఉంది.',
    'doesnt_end_with' => ':attribute ఫీల్డ్ కింద ఇచ్చిన వాటితో ముగియకూడదు: :values.',
    'doesnt_start_with' => ':attribute ఫీల్డ్ కింద ఇచ్చిన వాటితో ప్రారంభించకూడదు: :values.',
    'email' => ':attribute ఫీల్డ్ ఒక సరైన ఇమెయిల్ చిరునామా అయి ఉండాలి.',
    'ends_with' => ':attribute ఫీల్డ్ కింద ఇచ్చిన వాటితో ముగియాలి: :values.',
    'enum' => 'ఎంపిక చేసిన :attribute అమాన్యమైనది.',
    'exists' => 'ఎంపిక చేసిన :attribute అమాన్యమైనది.',
    'extensions' => ':attribute ఫీల్డ్ కింద ఇచ్చిన విస్తరణలలో ఒకదాన్ని కలిగి ఉండాలి: :values.',
    'file' => ':attribute ఫీల్డ్ ఒక ఫైల్ అయి ఉండాలి.',
    'filled' => ':attribute ఫీల్డ్ విలువ కలిగి ఉండాలి.',
    'gt' => [
        'array' => ':attribute ఫీల్డ్ :value కంటే ఎక్కువ అంశాలను కలిగి ఉండాలి.',
        'file' => ':attribute ఫీల్డ్ :value కిలోబైట్ల కంటే ఎక్కువ ఉండాలి.',
        'numeric' => ':attribute ఫీల్డ్ :value కంటే ఎక్కువ ఉండాలి.',
        'string' => ':attribute ఫీల్డ్ :value అక్షరాల కంటే ఎక్కువ ఉండాలి.',
    ],
    'gte' => [
        'array' => ':attribute ఫీల్డ్ :value అంశాలు లేదా ఎక్కువ ఉండాలి.',
        'file' => ':attribute ఫీల్డ్ :value కిలోబైట్ల కంటే ఎక్కువ లేదా సమానంగా ఉండాలి.',
        'numeric' => ':attribute ఫీల్డ్ :value కంటే ఎక్కువ లేదా సమానంగా ఉండాలి.',
        'string' => ':attribute ఫీల్డ్ :value అక్షరాల కంటే ఎక్కువ లేదా సమానంగా ఉండాలి.',
    ],
    'hex_color' => ':attribute ఫీల్డ్ సరైన హెక్సాడెసిమల్ రంగు అయి ఉండాలి.',
    'image' => ':attribute ఫీల్డ్ ఒక చిత్రం అయి ఉండాలి.',
    'in' => 'ఎంపిక చేసిన :attribute అమాన్యమైనది.',
    'in_array' => ':attribute ఫీల్డ్ :other లో ఉండాలి.',
    'integer' => ':attribute ఫీల్డ్ ఒక సంపూర్ణ సంఖ్య అయి ఉండాలి.',
    'ip' => ':attribute ఫీల్డ్ సరైన IP చిరునామా అయి ఉండాలి.',
    'ipv4' => ':attribute ఫీల్డ్ సరైన IPv4 చిరునామా అయి ఉండాలి.',
    'ipv6' => ':attribute ఫీల్డ్ సరైన IPv6 చిరునామా అయి ఉండాలి.',
    'json' => ':attribute ఫీల్డ్ సరైన JSON స్ట్రింగ్ అయి ఉండాలి.',
    'list' => ':attribute ఫీల్డ్ ఒక జాబితా అయి ఉండాలి.',
    'lowercase' => ':attribute ఫీల్డ్ తక్కువ కేసులో ఉండాలి.',
    'lt' => [
        'array' => ':attribute ఫీల్డ్ :value అంశాల కంటే తక్కువ ఉండాలి.',
        'file' => ':attribute ఫీల్డ్ :value కిలోబైట్ల కంటే తక్కువ ఉండాలి.',
        'numeric' => ':attribute ఫీల్డ్ :value కంటే తక్కువ ఉండాలి.',
        'string' => ':attribute ఫీల్డ్ :value అక్షరాల కంటే తక్కువ ఉండాలి.',
    ],
    'lte' => [
        'array' => ':attribute ఫీల్డ్ :value అంశాల కంటే ఎక్కువ ఉండకూడదు.',
        'file' => ':attribute ఫీల్డ్ :value కిలోబైట్ల కంటే తక్కువ లేదా సమానంగా ఉండాలి.',
        'numeric' => ':attribute ఫీల్డ్ :value కంటే తక్కువ లేదా సమానంగా ఉండాలి.',
        'string' => ':attribute ఫీల్డ్ :value అక్షరాల కంటే తక్కువ లేదా సమానంగా ఉండాలి.',
    ],
    'mac_address' => ':attribute ఫీల్డ్ సరైన MAC చిరునామా అయి ఉండాలి.',
    'max' => [
        'array' => ':attribute ఫీల్డ్ :max అంశాల కంటే ఎక్కువ ఉండకూడదు.',
        'file' => ':attribute ఫీల్డ్ :max కిలోబైట్ల కంటే ఎక్కువ ఉండకూడదు.',
        'numeric' => ':attribute ఫీల్డ్ :max కంటే ఎక్కువ ఉండకూడదు.',
        'string' => ':attribute ఫీల్డ్ :max అక్షరాల కంటే ఎక్కువ ఉండకూడదు.',
    ],
    'max_digits' => ':attribute ఫీల్డ్ :max అంకెల కంటే ఎక్కువ ఉండకూడదు.',
    'mimes' => ':attribute ఫీల్డ్ :values రకమైన ఫైల్ అయి ఉండాలి.',
    'mimetypes' => ':attribute ఫీల్డ్ :values రకమైన ఫైల్ అయి ఉండాలి.',
    'min' => [
        'array' => ':attribute ఫీల్డ్ కనీసం :min అంశాలను కలిగి ఉండాలి.',
        'file' => ':attribute ఫీల్డ్ కనీసం :min కిలోబైట్లు ఉండాలి.',
        'numeric' => ':attribute ఫీల్డ్ కనీసం :min ఉండాలి.',
        'string' => ':attribute ఫీల్డ్ కనీసం :min అక్షరాలు ఉండాలి.',
    ],
    'min_digits' => ':attribute ఫీల్డ్ కనీసం :min అంకెలు కలిగి ఉండాలి.',
    'missing' => ':attribute ఫీల్డ్ చవలు ఉండాలి.',
    'missing_if' => ':attribute ఫీల్డ్ :other :value ఉన్నప్పుడు చవలు ఉండాలి.',
    'missing_unless' => ':attribute ఫీల్డ్ :other :value లో ఉండకపోతే చవలు ఉండాలి.',
    'missing_with' => ':attribute ఫీల్డ్ :values ఉన్నప్పుడు చవలు ఉండాలి.',
    'missing_with_all' => ':attribute ఫీల్డ్ :values ఉన్నప్పుడు చవలు ఉండాలి.',
    'multiple_of' => ':attribute ఫీల్డ్ :value యొక్క విభజన కావాలి.',
    'not_in' => 'ఎంపిక చేసిన :attribute అమాన్యమైనది.',
    'not_regex' => ':attribute ఫీల్డ్ సరైన రూపంలో లేదు.',
    'numeric' => ':attribute ఫీల్డ్ ఒక సంఖ్య అయి ఉండాలి.',
    'password' => [
        'letters' => ':attribute ఫీల్డ్ కేవలం అక్షరాలను కలిగి ఉండాలి.',
        'mixed' => ':attribute ఫీల్డ్ ఒక పెద్ద అక్షరం మరియు ఒక చిన్న అక్షరాన్ని కలిగి ఉండాలి.',
        'numbers' => ':attribute ఫీల్డ్ ఒక సంఖ్యను కలిగి ఉండాలి.',
        'symbols' => ':attribute ఫీల్డ్ ఒక చిహ్నాన్ని కలిగి ఉండాలి.',
        'uncompromised' => 'ఇందులో :attribute లీక్ అయినది. దయచేసి మరోదాన్ని ప్రయత్నించండి.',
    ],
    'present' => ':attribute ఫీల్డ్ ఉంటే ఉండాలి.',
    'prohibited' => ':attribute ఫీల్డ్ నిషేధించబడింది.',
    'prohibited_if' => ':attribute ఫీల్డ్ :other :value ఉన్నప్పుడు నిషేధించబడింది.',
    'prohibited_unless' => ':attribute ఫీల్డ్ :other :values లో ఉండకపోతే నిషేధించబడింది.',
    'prohibits' => ':attribute ఫీల్డ్ :other ను ఉన్నప్పుడు నిషేధిస్తుంది.',
    'regex' => ':attribute ఫీల్డ్ సరైన రూపంలో లేదు.',
    'required' => ':attribute ఫీల్డ్ అవసరం.',
    'required_array_keys' => ':attribute ఫీల్డ్ అవసరమైన కీ లను కలిగి ఉండాలి: :values.',
    'required_if' => ':attribute ఫీల్డ్ :other :value ఉన్నప్పుడు అవసరం.',
    'required_if_accepted' => ':attribute ఫీల్డ్ :other ఆమోదించబడినప్పుడు అవసరం.',
    'required_unless' => ':attribute ఫీల్డ్ :other :values లో ఉండకపోతే అవసరం.',
    'required_with' => ':attribute ఫీల్డ్ :values ఉన్నప్పుడు అవసరం.',
    'required_with_all' => ':attribute ఫీల్డ్ :values ఉన్నప్పుడు అవసరం.',
    'required_without' => ':attribute ఫీల్డ్ :values లేనిప్పుడు అవసరం.',
    'required_without_all' => ':attribute ఫీల్డ్ ఎలాంటి :values లేనిప్పుడు అవసరం.',
    'same' => ':attribute ఫీల్డ్ మరియు :other సరిపోవాలి.',
    'size' => [
        'array' => ':attribute ఫీల్డ్ :size అంశాలను కలిగి ఉండాలి.',
        'file' => ':attribute ఫీల్డ్ :size కిలోబైట్లు ఉండాలి.',
        'numeric' => ':attribute ఫీల్డ్ :size ఉండాలి.',
        'string' => ':attribute ఫీల్డ్ :size అక్షరాలను కలిగి ఉండాలి.',
    ],
    'starts_with' => ':attribute ఫీల్డ్ కింద ఇచ్చిన వాటితో ప్రారంభించాలి: :values.',
    'string' => ':attribute ఫీల్డ్ ఒక స్ట్రింగ్ అయి ఉండాలి.',
    'timezone' => ':attribute ఫీల్డ్ ఒక సరైన టైమ్‌జోన్ అయి ఉండాలి.',
    'unique' => ':attribute ఇప్పటికే తీసుకున్నది.',
    'uploaded' => ':attribute ఫైల్ అప్‌లోడ్ చేయడంలో విఫలమైంది.',
    'url' => ':attribute ఫీల్డ్ సరైన URL అయి ఉండాలి.',
    'uuid' => ':attribute ఫీల్డ్ సరైన UUID అయి ఉండాలి.',


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
