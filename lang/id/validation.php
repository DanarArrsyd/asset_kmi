<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Only the rules this application actually uses are translated. Anything
    | else falls back to the English file, which is the correct behaviour for a
    | message nobody has had a reason to phrase in Indonesian yet.
    |
    | Keep this in step with app/Http/Requests and the master-data controllers.
    |
    */

    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Password yang Anda masukkan salah.',
    'date' => ':attribute harus berupa tanggal yang benar.',
    'dimensions' => 'Ukuran gambar :attribute tidak sesuai.',
    'email' => ':attribute harus berupa alamat email yang benar.',
    'enum' => 'Pilihan :attribute tidak berlaku.',
    'exists' => 'Pilihan :attribute tidak ada di data.',
    'image' => ':attribute harus berupa gambar.',
    'in' => 'Pilihan :attribute tidak berlaku.',
    'lowercase' => ':attribute harus huruf kecil semua.',
    'max' => [
        'array' => ':attribute maksimal :max item.',
        'file' => ':attribute maksimal :max kilobyte.',
        'numeric' => ':attribute maksimal :max.',
        'string' => ':attribute maksimal :max karakter.',
    ],
    'mimes' => ':attribute harus berupa file bertipe: :values.',
    'mimetypes' => ':attribute harus berupa file bertipe: :values.',
    'min' => [
        'array' => ':attribute minimal :min item.',
        'file' => ':attribute minimal :min kilobyte.',
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
    ],
    'numeric' => ':attribute harus berupa angka.',
    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi jika :other bernilai :value.',
    'string' => ':attribute harus berupa teks.',
    'unique' => ':attribute sudah dipakai.',
    'uploaded' => ':attribute gagal diunggah. Cek ukuran filenya.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Field names as they appear on the forms, so a message reads "Nama Asset
    | wajib diisi" rather than naming the database column.
    |
    */

    'attributes' => [
        'brand_id' => 'Brand',
        'category_id' => 'Kategori',
        'code' => 'Kode',
        'condition' => 'Kondisi',
        'current_password' => 'Password Saat Ini',
        'department_id' => 'Departemen',
        'email' => 'Email',
        'location_id' => 'Lokasi',
        'model' => 'Model',
        'name' => 'Nama',
        'notes' => 'Catatan',
        'password' => 'Password',
        'password_confirmation' => 'Konfirmasi Password',
        'photo' => 'Foto',
        'pic' => 'PIC',
        'purchase_date' => 'Tanggal Pembelian',
        'role' => 'Role',
        'specification' => 'Spesifikasi',
        'status' => 'Status',
    ],

];
