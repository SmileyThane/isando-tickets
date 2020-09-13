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

    'accepted' => 'The :attribute muss akzeptiert werden.',
    'active_url' => 'The :attribute ist keine gültige URL.',
    'after' => 'The :attribute muss ein Datum sein nach :date.',
    'after_or_equal' => 'The :attribute muss ein Datum nach oder gleich :date.',
    'alpha' => 'The :attribute darf nur Buchstaben enthalten.',
    'alpha_dash' => 'The :attribute darf nur Buchstaben, Zahlen, Bindestriche und Unterstriche enthalten.',
    'alpha_num' => 'The :attribute darf nur Buchstaben und Zahlen enthalten.',
    'array' => 'The :attribute muss ein Feld sein.',
    'before' => 'The :attribute muss ein Datum vor :date sein.',
    'before_or_equal' => 'The :attribute muss ein Datum vor oder gleich :date sein.',
    'between' => [
        'numeric' => 'The :attribute muss zwischen :min und :max liegen.',
        'file' => 'The :attribute muss zwischen :min und :max Kilobyte liegen.',
        'string' => 'The :attribute muss zwischen den Zeichen :min und :max liegen.',
        'array' => 'The :attribute muss zwischen dem Artikel :min and :max liegen.',
    ],
    'boolean' => 'The :attribute das Feld muss wahr oder falsch sein.',
    'confirmed' => 'The :attribute Bestätigung stimmt nicht überein.',
    'date' => 'The :attribute ist kein gültiges Datum.',
    'date_equals' => 'The :attribute muss ein Datum sein, das :date entspricht.',
    'date_format' => 'The :attribute entspricht nicht  dem Format :format.',
    'different' => 'The :attribute und :other müssen unterschiedlich sein.',
    'digits' => 'The :attribute und :other müssen unterschiedlich sein.',
    'digits_between' => 'The :attribute muss zwischen den Ziffern :min und :max liegen.',
    'dimensions' => 'The :attribute hat ungültige Bilddimensionen.',
    'distinct' => 'The :attribute hat einen doppelten Wert.',
    'email' => 'The :attribute muss eine gültige E-Mail-Adresse sein.',
    'ends_with' => 'The :attribute muss mit einem der folgenden Zeichen :values enden.',
    'exists' => 'Das ausgewählte :attribute ist ungültig.',
    'file' => 'Das ausgewählte :attribute ist ungültig.',
    'filled' => 'The :attribute Feld muss einen Wert haben.',
    'gt' => [
        'numeric' => 'The :attribute muss grösser als der :value sein.',
        'file' => 'The :attribute muss grösser als Kilobyte :value sein.',
        'string' => 'The :attribute muss grösser als die :value Zeichen sein.',
        'array' => 'The :attribute muss mehr als :value Elemente haben.',
    ],
    'gte' => [
        'numeric' => 'The :attribute muss grösser oder gleich dem :value sein.',
        'file' => 'The :attribute muss grösser oder gleich dem :value in Kilobyte sein.',
        'string' => 'The :attribute muss grösser oder gleich den :value Zeichen sein.',
        'array' => 'The :attribute muss :value Elemente oder mehr haben.',
    ],
    'image' => 'The :attribute muss ein Bild sein.',
    'in' => 'Das ausgewählte :attribute ist ungültig.',
    'in_array' => 'Das Feld :attribute existiert nicht in :other.',
    'integer' => 'The :attribute muss eine ganze Zahl sein.',
    'ip' => 'The :attribute muss eine gültige IP-Adresse sein.',
    'ipv4' => 'The :attribute muss eine gültige IPv4 Adresse sein.',
    'ipv6' => 'The :attribute muss eine gültige IPv6 Adresse sein.',
    'json' => 'The :attribute muss eine gültige JSON Zeichenkette sein.',
    'lt' => [
        'numeric' => 'The :attribute muss kleiner als der :value sein.',
        'file' => 'The :attribute muss kleiner als Kilobyte :value sein.',
        'string' => 'The :attribute muss kleiner als die :value Zeichen sein.',
        'array' => 'The :attribute muss weniger als :value Elemente haben.',
    ],
    'lte' => [
        'numeric' => 'The :attribute muss kleiner oder gleich dem :value sein.',
        'file' => 'The :attribute muss kleiner oder gleich dem :value in Kilobyte sein.',
        'string' => 'The :attribute muss kleiner oder gleich dem :value Zeichen sein.',
        'array' => 'The :attribute darf nicht mehr als :value Elemente haben.',
    ],
    'max' => [
        'numeric' => 'The :attribute darf nicht grösser als :max sein.',
        'file' => 'The :attribute darf nicht grösser als :max Kilobyte sein.',
        'string' => 'The :attribute darf nicht grösser als die :max Zeichen sein.',
        'array' => 'The :attribute darf nicht mehr als :max Elemente enthalten.',
    ],
    'mimes' => 'The :attribute muss eine Datei vom Typ: :values sein.',
    'mimetypes' => 'The :attribute muss eine Datei vom Typ: :values sein.',
    'min' => [
        'numeric' => 'The :attribute muss mindestens :min sein.',
        'file' => 'The :attribute muss mindestens :min Kilobyte betragen.',
        'string' => 'The :attribute muss mindestens :min Zeichen enthalten.',
        'array' => 'The :attribute muss mindestens :min Elemente enthalten.',
    ],
    'not_in' => 'Das ausgewählte :attribute ist ungültig.',
    'not_regex' => 'The :attribute Format ist ungültig.',
    'numeric' => 'The :attribute muss eine Zahl sein.',
    'password' => 'Das Passwort ist falsch.',
    'present' => 'The :attribute Feld muss vorhanden sein.',
    'regex' => 'The :attribute Format ist ungültig.',
    'required' => 'The :attribute Feld ist erforderlich.',
    'required_if' => 'The :attribute Feld ist erforderlich, wenn :other :value ist.',
    'required_unless' => 'The :attribute Feld ist erforderlich, es sei denn,  :other ist in :values enthalten.',
    'required_with' => 'The :attribute Feld ist erforderlich, wenn :values vorhanden ist.',
    'required_with_all' => 'The :attribute Feld ist erforderlich, wenn :values vorhanden sind.',
    'required_without' => 'The :attribute Feld ist erforderlich, wenn :values nicht vorhanden ist.',
    'required_without_all' => 'The :attribute Feld ist erforderlich wenn, keiner der :values vorhanden ist.',
    'same' => 'The :attribute und :other müssen übereinstimmen.',
    'size' => [
        'numeric' => 'The :attribute muss :size sein.',
        'file' => 'The :attribute muss die Grösse :size Kilobyte haben.',
        'string' => 'The :attribute muss :size Zeichen sein.',
        'array' => 'The :attribute muss :size items enthalten.',
    ],
    'starts_with' => 'The :attribute muss mit einem der folgenden Zeichen: :values beginnen.',
    'string' => 'The :attribute muss eine Zeichenkette sein.',
    'timezone' => 'The :attribute muss eine gültige Zone sein.',
    'unique' => 'The :attribute ist bereits vergeben.',
    'uploaded' => 'The :attribute konnte nicht hochgeladen werden.',
    'url' => 'The :attribute Format ist ungültig.',
    'uuid' => 'The :attribute muss eine gültige UUID sein.',

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
