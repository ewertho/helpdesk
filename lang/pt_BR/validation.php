<?php

// Só as regras que o sistema usa. Acrescente aqui ao usar uma regra nova.
return [
    'required' => 'Preencha o campo :attribute.',
    'string' => 'O campo :attribute precisa ser um texto.',
    'email' => 'Informe um e-mail válido.',
    'unique' => 'Este :attribute já está em uso.',
    'confirmed' => 'A confirmação de :attribute não confere.',
    'exists' => 'O valor escolhido para :attribute não existe.',
    'enum' => 'O valor escolhido para :attribute não é válido.',
    'file' => 'O campo :attribute precisa ser um arquivo.',
    'mimes' => 'O :attribute precisa ser do tipo: :values.',
    'min' => [
        'string' => 'O campo :attribute precisa de pelo menos :min caracteres.',
    ],
    'max' => [
        'string' => 'O campo :attribute aceita no máximo :max caracteres.',
        'file' => 'O :attribute pode ter no máximo :max KB.',
    ],
    'password' => [
        'min' => 'A senha precisa de pelo menos :min caracteres.',
    ],

    'attributes' => [
        'name' => 'nome',
        'email' => 'e-mail',
        'password' => 'senha',
        'subject' => 'assunto',
        'description' => 'descrição',
        'category' => 'categoria',
        'priority' => 'urgência',
        'attachment' => 'anexo',
        'body' => 'resposta',
        'role' => 'papel',
        'assignee_id' => 'responsável',
    ],
];
