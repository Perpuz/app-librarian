<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title'       => 'The Great Gatsby',
                'author'      => 'F. Scott Fitzgerald',
                'isbn'        => '9780743273565',
                'stock'       => 5,
                'cover_url'   => 'https://covers.openlibrary.org/b/id/8432047-M.jpg',
                'description' => 'A classic novel of the Jazz Age.',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'To Kill a Mockingbird',
                'author'      => 'Harper Lee',
                'isbn'        => '9780061120084',
                'stock'       => 3,
                'cover_url'   => 'https://covers.openlibrary.org/b/id/8225266-M.jpg',
                'description' => 'A novel about the serious issues of rape and racial inequality.',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => '1984',
                'author'      => 'George Orwell',
                'isbn'        => '9780451524935',
                'stock'       => 8,
                'cover_url'   => 'https://covers.openlibrary.org/b/id/7222246-M.jpg',
                'description' => 'Dystopian social science fiction novel and cautionary tale.',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Harry Potter and the Philosopher\'s Stone',
                'author'      => 'J.K. Rowling',
                'isbn'        => '9780747532743',
                'stock'       => 10,
                'cover_url'   => 'https://covers.openlibrary.org/b/id/10522912-M.jpg',
                'description' => 'The first novel in the Harry Potter series.',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'The Hobbit',
                'author'      => 'J.R.R. Tolkien',
                'isbn'        => '9780547928227',
                'stock'       => 4,
                'cover_url'   => 'https://covers.openlibrary.org/b/id/8406786-M.jpg',
                'description' => 'A children\'s fantasy novel.',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Pride and Prejudice',
                'author'      => 'Jane Austen',
                'isbn'        => '9780141439518',
                'stock'       => 6,
                'cover_url'   => 'https://covers.openlibrary.org/b/id/8231856-M.jpg',
                'description' => 'A romantic novel of manners.',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'The Catcher in the Rye',
                'author'      => 'J.D. Salinger',
                'isbn'        => '9780316769488',
                'stock'       => 2,
                'cover_url'   => 'https://covers.openlibrary.org/b/id/8231990-M.jpg',
                'description' => 'A story about teenage angst and alienation.',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'The Lord of the Rings',
                'author'      => 'J.R.R. Tolkien',
                'isbn'        => '9780544003415',
                'stock'       => 7,
                'cover_url'   => 'https://covers.openlibrary.org/b/id/8386109-M.jpg',
                'description' => 'High fantasy novel.',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Animal Farm',
                'author'      => 'George Orwell',
                'isbn'        => '9780451526342',
                'stock'       => 5,
                'cover_url'   => 'https://covers.openlibrary.org/b/id/7222236-M.jpg',
                'description' => 'A beast fable.',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'The Da Vinci Code',
                'author'      => 'Dan Brown',
                'isbn'        => '9780307474278',
                'stock'       => 3,
                'cover_url'   => 'https://covers.openlibrary.org/b/id/6542676-M.jpg',
                'description' => 'Mystery thriller novel.',
                'created_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Using Query Builder
        $this->db->table('books')->insertBatch($data);
    }
}
