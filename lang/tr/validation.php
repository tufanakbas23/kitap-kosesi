<?php

/*
| Türkçe doğrulama mesajları (projede kullanılan kurallar).
| Bulunmayan kurallar için Laravel İngilizce mesaja geri döner.
*/

return [
    'accepted'         => ':attribute kabul edilmelidir.',
    'alpha_num'        => ':attribute sadece harf ve rakamlardan oluşmalıdır.',
    'before'           => ':attribute :date tarihinden önce olmalıdır.',
    'between'          => [
        'numeric' => ':attribute :min ile :max arasında olmalıdır.',
        'string'  => ':attribute :min ile :max karakter arasında olmalıdır.',
    ],
    'boolean'          => ':attribute alanı doğru veya yanlış olmalıdır.',
    'confirmed'        => ':attribute tekrarı eşleşmiyor.',
    'current_password' => 'Şifre hatalı.',
    'date'             => ':attribute geçerli bir tarih olmalıdır.',
    'different'        => ':attribute ile :other birbirinden farklı olmalıdır.',
    'digits'           => ':attribute :digits haneli olmalıdır.',
    'email'            => ':attribute geçerli bir e-posta adresi olmalıdır.',
    'exists'           => 'Seçilen :attribute geçersiz.',
    'gte'              => [
        'numeric' => ':attribute, :value değerinden büyük veya eşit olmalıdır.',
    ],
    'image'            => ':attribute bir resim dosyası olmalıdır.',
    'in'               => 'Seçilen :attribute geçersiz.',
    'integer'          => ':attribute tam sayı olmalıdır.',
    'max'              => [
        'file'    => ':attribute :max kilobayttan büyük olamaz.',
        'numeric' => ':attribute :max değerinden büyük olamaz.',
        'string'  => ':attribute :max karakterden uzun olamaz.',
    ],
    'min'              => [
        'numeric' => ':attribute en az :min olmalıdır.',
        'string'  => ':attribute en az :min karakter olmalıdır.',
    ],
    'numeric'          => ':attribute sayı olmalıdır.',
    'password'         => [
        'letters' => ':attribute en az bir harf içermelidir.',
        'numbers' => ':attribute en az bir rakam içermelidir.',
        'mixed'   => ':attribute en az bir büyük ve bir küçük harf içermelidir.',
        'symbols' => ':attribute en az bir sembol içermelidir.',
    ],
    'regex'            => ':attribute biçimi geçersiz.',
    'required'         => ':attribute alanı zorunludur.',
    'required_if'      => ':other :value olduğunda :attribute alanı zorunludur.',
    'string'           => ':attribute metin olmalıdır.',
    'unique'           => 'Bu :attribute zaten kullanılıyor.',

    'attributes' => [
        'name'                  => 'ad',
        'surname'               => 'soyad',
        'email'                 => 'e-posta',
        'phone'                 => 'telefon',
        'password'              => 'şifre',
        'password_confirmation' => 'şifre tekrarı',
        'current_password'      => 'mevcut şifre',
        'birth_date'            => 'doğum tarihi',
        'gender'                => 'cinsiyet',
        'terms'                 => 'üyelik sözleşmesi',
        'status'                => 'durum',
        'admin_note'            => 'yönetici notu',
        'cargo_company'         => 'kargo firması',
        'tracking_number'       => 'takip numarası',
    ],
];
