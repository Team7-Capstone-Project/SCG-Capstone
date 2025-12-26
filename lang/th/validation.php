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

    'accepted' => 'ฟิลด์ :attribute ต้องได้รับการยอมรับ',
    'accepted_if' => 'ฟิลด์ :attribute ต้องได้รับการยอมรับเมื่อ :other เป็น :value',
    'active_url' => 'ฟิลด์ :attribute ต้องเป็น URL ที่ถูกต้อง',
    'after' => 'ฟิลด์ :attribute ต้องเป็นวันที่หลังจาก :date',
    'after_or_equal' => 'ฟิลด์ :attribute ต้องเป็นวันที่หลังจากหรือเท่ากับ :date',
    'alpha' => 'ฟิลด์ :attribute ต้องมีเฉพาะตัวอักษรเท่านั้น',
    'alpha_dash' => 'ฟิลด์ :attribute ต้องมีเฉพาะตัวอักษร ตัวเลข เครื่องหมายขีดกลาง และขีดล่างเท่านั้น',
    'alpha_num' => 'ฟิลด์ :attribute ต้องมีเฉพาะตัวอักษรและตัวเลขเท่านั้น',
    'any_of' => 'ฟิลด์ :attribute ไม่ถูกต้อง',
    'array' => 'ฟิลด์ :attribute ต้องเป็นอาร์เรย์',
    'ascii' => 'ฟิลด์ :attribute ต้องมีเฉพาะอักขระและสัญลักษณ์แบบไบต์เดียวเท่านั้น',
    'before' => 'ฟิลด์ :attribute ต้องเป็นวันที่ก่อน :date',
    'before_or_equal' => 'ฟิลด์ :attribute ต้องเป็นวันที่ก่อนหรือเท่ากับ :date',
    'between' => [
        'array' => 'ฟิลด์ :attribute ต้องมีระหว่าง :min ถึง :max รายการ',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดระหว่าง :min ถึง :max กิโลไบต์',
        'numeric' => 'ฟิลด์ :attribute ต้องอยู่ระหว่าง :min ถึง :max',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวระหว่าง :min ถึง :max ตัวอักษร',
    ],
    'boolean' => 'ฟิลด์ :attribute ต้องเป็นจริงหรือเท็จ',
    'can' => 'ฟิลด์ :attribute มีค่าที่ไม่ได้รับอนุญาต',
    'confirmed' => 'การยืนยันฟิลด์ :attribute ไม่ตรงกัน',
    'contains' => 'ฟิลด์ :attribute ขาดค่าที่จำเป็น',
    'current_password' => 'รหัสผ่านไม่ถูกต้อง',
    'date' => 'ฟิลด์ :attribute ต้องเป็นวันที่ที่ถูกต้อง',
    'date_equals' => 'ฟิลด์ :attribute ต้องเป็นวันที่เท่ากับ :date',
    'date_format' => 'ฟิลด์ :attribute ต้องตรงกับรูปแบบ :format',
    'decimal' => 'ฟิลด์ :attribute ต้องมี :decimal ตำแหน่งทศนิยม',
    'declined' => 'ฟิลด์ :attribute ต้องถูกปฏิเสธ',
    'declined_if' => 'ฟิลด์ :attribute ต้องถูกปฏิเสธเมื่อ :other เป็น :value',
    'different' => 'ฟิลด์ :attribute และ :other ต้องแตกต่างกัน',
    'digits' => 'ฟิลด์ :attribute ต้องเป็น :digits หลัก',
    'digits_between' => 'ฟิลด์ :attribute ต้องอยู่ระหว่าง :min ถึง :max หลัก',
    'dimensions' => 'ฟิลด์ :attribute มีขนาดภาพที่ไม่ถูกต้อง',
    'distinct' => 'ฟิลด์ :attribute มีค่าที่ซ้ำกัน',
    'doesnt_contain' => 'ฟิลด์ :attribute ต้องไม่มีค่าต่อไปนี้: :values',
    'doesnt_end_with' => 'ฟิลด์ :attribute ต้องไม่ลงท้ายด้วยค่าต่อไปนี้: :values',
    'doesnt_start_with' => 'ฟิลด์ :attribute ต้องไม่เริ่มต้นด้วยค่าต่อไปนี้: :values',
    'email' => 'ฟิลด์ :attribute ต้องเป็นที่อยู่อีเมลที่ถูกต้อง',
    'encoding' => 'ฟิลด์ :attribute ต้องเข้ารหัสด้วย :encoding',
    'ends_with' => 'ฟิลด์ :attribute ต้องลงท้ายด้วยค่าต่อไปนี้: :values',
    'enum' => ':attribute ที่เลือกไม่ถูกต้อง',
    'exists' => ':attribute ที่เลือกไม่ถูกต้อง',
    'extensions' => 'ฟิลด์ :attribute ต้องมีนามสกุลต่อไปนี้: :values',
    'file' => 'ฟิลด์ :attribute ต้องเป็นไฟล์',
    'filled' => 'ฟิลด์ :attribute ต้องมีค่า',
    'gt' => [
        'array' => 'ฟิลด์ :attribute ต้องมีมากกว่า :value รายการ',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดมากกว่า :value กิโลไบต์',
        'numeric' => 'ฟิลด์ :attribute ต้องมากกว่า :value',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวมากกว่า :value ตัวอักษร',
    ],
    'gte' => [
        'array' => 'ฟิลด์ :attribute ต้องมี :value รายการหรือมากกว่า',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดมากกว่าหรือเท่ากับ :value กิโลไบต์',
        'numeric' => 'ฟิลด์ :attribute ต้องมากกว่าหรือเท่ากับ :value',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวมากกว่าหรือเท่ากับ :value ตัวอักษร',
    ],
    'hex_color' => 'ฟิลด์ :attribute ต้องเป็นสีเลขฐานสิบหกที่ถูกต้อง',
    'image' => 'ฟิลด์ :attribute ต้องเป็นรูปภาพ',
    'in' => ':attribute ที่เลือกไม่ถูกต้อง',
    'in_array' => 'ฟิลด์ :attribute ต้องมีอยู่ใน :other',
    'in_array_keys' => 'ฟิลด์ :attribute ต้องมีคีย์ต่อไปนี้อย่างน้อยหนึ่งรายการ: :values',
    'integer' => 'ฟิลด์ :attribute ต้องเป็นจำนวนเต็ม',
    'ip' => 'ฟิลด์ :attribute ต้องเป็นที่อยู่ IP ที่ถูกต้อง',
    'ipv4' => 'ฟิลด์ :attribute ต้องเป็นที่อยู่ IPv4 ที่ถูกต้อง',
    'ipv6' => 'ฟิลด์ :attribute ต้องเป็นที่อยู่ IPv6 ที่ถูกต้อง',
    'json' => 'ฟิลด์ :attribute ต้องเป็นสตริง JSON ที่ถูกต้อง',
    'list' => 'ฟิลด์ :attribute ต้องเป็นรายการ',
    'lowercase' => 'ฟิลด์ :attribute ต้องเป็นตัวพิมพ์เล็ก',
    'lt' => [
        'array' => 'ฟิลด์ :attribute ต้องมีน้อยกว่า :value รายการ',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดน้อยกว่า :value กิโลไบต์',
        'numeric' => 'ฟิลด์ :attribute ต้องน้อยกว่า :value',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวน้อยกว่า :value ตัวอักษร',
    ],
    'lte' => [
        'array' => 'ฟิลด์ :attribute ต้องมีไม่เกิน :value รายการ',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดน้อยกว่าหรือเท่ากับ :value กิโลไบต์',
        'numeric' => 'ฟิลด์ :attribute ต้องน้อยกว่าหรือเท่ากับ :value',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวน้อยกว่าหรือเท่ากับ :value ตัวอักษร',
    ],
    'mac_address' => 'ฟิลด์ :attribute ต้องเป็นที่อยู่ MAC ที่ถูกต้อง',
    'max' => [
        'array' => 'ฟิลด์ :attribute ต้องมีไม่เกิน :max รายการ',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดไม่เกิน :max กิโลไบต์',
        'numeric' => 'ฟิลด์ :attribute ต้องไม่เกิน :max',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวไม่เกิน :max ตัวอักษร',
    ],
    'max_digits' => 'ฟิลด์ :attribute ต้องมีไม่เกิน :max หลัก',
    'mimes' => 'ฟิลด์ :attribute ต้องเป็นไฟล์ประเภท: :values',
    'mimetypes' => 'ฟิลด์ :attribute ต้องเป็นไฟล์ประเภท: :values',
    'min' => [
        'array' => 'ฟิลด์ :attribute ต้องมีอย่างน้อย :min รายการ',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาดอย่างน้อย :min กิโลไบต์',
        'numeric' => 'ฟิลด์ :attribute ต้องมีค่าอย่างน้อย :min',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาวอย่างน้อย :min ตัวอักษร',
    ],
    'min_digits' => 'ฟิลด์ :attribute ต้องมีอย่างน้อย :min หลัก',
    'missing' => 'ฟิลด์ :attribute ต้องหายไป',
    'missing_if' => 'ฟิลด์ :attribute ต้องหายไปเมื่อ :other เป็น :value',
    'missing_unless' => 'ฟิลด์ :attribute ต้องหายไปเว้นแต่ :other อยู่ใน :value',
    'missing_with' => 'ฟิลด์ :attribute ต้องหายไปเมื่อมี :values',
    'missing_with_all' => 'ฟิลด์ :attribute ต้องหายไปเมื่อมี :values',
    'multiple_of' => 'ฟิลด์ :attribute ต้องเป็นพหุคูณของ :value',
    'not_in' => ':attribute ที่เลือกไม่ถูกต้อง',
    'not_regex' => 'รูปแบบฟิลด์ :attribute ไม่ถูกต้อง',
    'numeric' => 'ฟิลด์ :attribute ต้องเป็นตัวเลข',
    'password' => [
        'letters' => 'ฟิลด์ :attribute ต้องมีตัวอักษรอย่างน้อยหนึ่งตัว',
        'mixed' => 'ฟิลด์ :attribute ต้องมีตัวอักษรพิมพ์ใหญ่และพิมพ์เล็กอย่างน้อยหนึ่งตัว',
        'numbers' => 'ฟิลด์ :attribute ต้องมีตัวเลขอย่างน้อยหนึ่งตัว',
        'symbols' => 'ฟิลด์ :attribute ต้องมีสัญลักษณ์อย่างน้อยหนึ่งตัว',
        'uncompromised' => ':attribute ที่ให้มาปรากฏในการรั่วไหลของข้อมูล กรุณาเลือก :attribute อื่น',
    ],
    'present' => 'ฟิลด์ :attribute ต้องมีอยู่',
    'present_if' => 'ฟิลด์ :attribute ต้องมีอยู่เมื่อ :other เป็น :value',
    'present_unless' => 'ฟิลด์ :attribute ต้องมีอยู่เว้นแต่ :other เป็น :value',
    'present_with' => 'ฟิลด์ :attribute ต้องมีอยู่เมื่อมี :values',
    'present_with_all' => 'ฟิลด์ :attribute ต้องมีอยู่เมื่อมี :values',
    'prohibited' => 'ฟิลด์ :attribute ถูกห้าม',
    'prohibited_if' => 'ฟิลด์ :attribute ถูกห้ามเมื่อ :other เป็น :value',
    'prohibited_if_accepted' => 'ฟิลด์ :attribute ถูกห้ามเมื่อ :other ได้รับการยอมรับ',
    'prohibited_if_declined' => 'ฟิลด์ :attribute ถูกห้ามเมื่อ :other ถูกปฏิเสธ',
    'prohibited_unless' => 'ฟิลด์ :attribute ถูกห้ามเว้นแต่ :other อยู่ใน :values',
    'prohibits' => 'ฟิลด์ :attribute ห้าม :other จากการมีอยู่',
    'regex' => 'รูปแบบฟิลด์ :attribute ไม่ถูกต้อง',
    'required' => 'ฟิลด์ :attribute จำเป็นต้องกรอก',
    'required_array_keys' => 'ฟิลด์ :attribute ต้องมีรายการสำหรับ: :values',
    'required_if' => 'ฟิลด์ :attribute จำเป็นต้องกรอกเมื่อ :other เป็น :value',
    'required_if_accepted' => 'ฟิลด์ :attribute จำเป็นต้องกรอกเมื่อ :other ได้รับการยอมรับ',
    'required_if_declined' => 'ฟิลด์ :attribute จำเป็นต้องกรอกเมื่อ :other ถูกปฏิเสธ',
    'required_unless' => 'ฟิลด์ :attribute จำเป็นต้องกรอกเว้นแต่ :other อยู่ใน :values',
    'required_with' => 'ฟิลด์ :attribute จำเป็นต้องกรอกเมื่อมี :values',
    'required_with_all' => 'ฟิลด์ :attribute จำเป็นต้องกรอกเมื่อมี :values',
    'required_without' => 'ฟิลด์ :attribute จำเป็นต้องกรอกเมื่อไม่มี :values',
    'required_without_all' => 'ฟิลด์ :attribute จำเป็นต้องกรอกเมื่อไม่มี :values ทั้งหมด',
    'same' => 'ฟิลด์ :attribute ต้องตรงกับ :other',
    'size' => [
        'array' => 'ฟิลด์ :attribute ต้องมี :size รายการ',
        'file' => 'ฟิลด์ :attribute ต้องมีขนาด :size กิโลไบต์',
        'numeric' => 'ฟิลด์ :attribute ต้องเป็น :size',
        'string' => 'ฟิลด์ :attribute ต้องมีความยาว :size ตัวอักษร',
    ],
    'starts_with' => 'ฟิลด์ :attribute ต้องเริ่มต้นด้วยค่าต่อไปนี้: :values',
    'string' => 'ฟิลด์ :attribute ต้องเป็นสตริง',
    'timezone' => 'ฟิลด์ :attribute ต้องเป็นโซนเวลาที่ถูกต้อง',
    'unique' => ':attribute ถูกใช้ไปแล้ว',
    'uploaded' => ':attribute อัปโหลดล้มเหลว',
    'uppercase' => 'ฟิลด์ :attribute ต้องเป็นตัวพิมพ์ใหญ่',
    'url' => 'ฟิลด์ :attribute ต้องเป็น URL ที่ถูกต้อง',
    'ulid' => 'ฟิลด์ :attribute ต้องเป็น ULID ที่ถูกต้อง',
    'uuid' => 'ฟิลด์ :attribute ต้องเป็น UUID ที่ถูกต้อง',

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
