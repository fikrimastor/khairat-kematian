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

    'accepted' => ':attribute mesti diterima.',
    'accepted_if' => ':attribute mesti diterima apabila :other adalah :value.',
    'active_url' => ':attribute bukan URL yang sah.',
    'after' => ':attribute mesti tarikh selepas :date.',
    'after_or_equal' => ':attribute mesti tarikh selepas atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh mengandungi huruf.',
    'alpha_dash' => ':attribute hanya boleh mengandungi huruf, nombor, sengkang dan garis bawah.',
    'alpha_num' => ':attribute hanya boleh mengandungi huruf dan nombor.',
    'array' => ':attribute mesti jadi array.',
    'before' => ':attribute mesti tarikh sebelum :date.',
    'before_or_equal' => ':attribute mesti tarikh sebelum atau sama dengan :date.',
    'between' => [
        'numeric' => ':attribute mesti antara :min dan :max.',
        'file' => ':attribute mesti antara :min dan :max kilobait.',
        'string' => ':attribute mesti antara :min dan :max aksara.',
        'array' => ':attribute mesti mempunyai antara :min dan :max item.',
    ],
    'boolean' => 'Medan :attribute mesti benar atau palsu.',
    'confirmed' => 'Pengesahan :attribute tidak sepadan.',
    'current_password' => 'Kata laluan tidak betul.',
    'date' => ':attribute bukan tarikh yang sah.',
    'email' => ':attribute mesti alamat e-mel yang sah.',
    'exists' => ':attribute yang dipilih tidak sah.',
    'file' => ':attribute mesti fail.',
    'filled' => 'Medan :attribute mesti mempunyai nilai.',
    'image' => ':attribute mesti imej.',
    'in' => ':attribute yang dipilih tidak sah.',
    'integer' => ':attribute mesti integer.',
    'ip' => ':attribute mesti alamat IP yang sah.',
    'json' => ':attribute mesti rentetan JSON yang sah.',
    'max' => [
        'numeric' => ':attribute tidak boleh melebihi :max.',
        'file' => ':attribute tidak boleh melebihi :max kilobait.',
        'string' => ':attribute tidak boleh melebihi :max aksara.',
        'array' => ':attribute tidak boleh mempunyai lebih daripada :max item.',
    ],
    'mimes' => ':attribute mesti fail jenis: :values.',
    'min' => [
        'numeric' => ':attribute mesti sekurang-kurangnya :min.',
        'file' => ':attribute mesti sekurang-kurangnya :min kilobait.',
        'string' => ':attribute mesti sekurang-kurangnya :min aksara.',
        'array' => ':attribute mesti mempunyai sekurang-kurangnya :min item.',
    ],
    'numeric' => ':attribute mesti nombor.',
    'password' => 'Kata laluan tidak betul.',
    'present' => 'Medan :attribute mesti ada.',
    'regex' => 'Format :attribute tidak sah.',
    'required' => 'Medan :attribute diperlukan.',
    'required_if' => 'Medan :attribute diperlukan apabila :other adalah :value.',
    'required_with' => 'Medan :attribute diperlukan apabila :values ada.',
    'required_without' => 'Medan :attribute diperlukan apabila :values tiada.',
    'same' => ':attribute dan :other mesti sepadan.',
    'size' => [
        'numeric' => ':attribute mesti :size.',
        'file' => ':attribute mesti :size kilobait.',
        'string' => ':attribute mesti :size aksara.',
        'array' => ':attribute mesti mengandungi :size item.',
    ],
    'string' => ':attribute mesti rentetan.',
    'timezone' => ':attribute mesti zon masa yang sah.',
    'unique' => ':attribute telah diambil.',
    'uploaded' => ':attribute gagal dimuat naik.',
    'url' => ':attribute mesti URL yang sah.',
    'uuid' => ':attribute mesti UUID yang sah.',

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
