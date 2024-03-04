<?php

namespace App\Models;



class Post
{
    private static $blog_posts = [
        [
            "title" => "Judul Post aaaaa",
            "slug" => "judul-post-pertama",
            "author" => "Aldi Fauzan",
            "body" => "Lorem ipsum dolor sit amet consectetur adipisicing elit. Expedita porro aliquid distinctio similique eius recusandae velit iusto inventore id ducimus. Hic ducimus ea blanditiis voluptatem porro, non impedit magnam, aperiam recusandae veniam sunt repudiandae excepturi odit soluta error, qui harum laboriosam dolorem dolore? Voluptas tempore numquam quaerat, sequi doloremque odit accusantium temporibus amet debitis commodi veritatis magnam cupiditate recusandae voluptatum tenetur, ipsum quis, dolorem ex nemo. Maxime, provident nostrum mollitia id ullam accusantium consequatur eligendi soluta atque voluptates necessitatibus incidunt."
        ],
        [
            "title" => "Judul Post Kedua",
            "slug" => "judul-post-kedua",
            "author" => "Aldi",
            "body" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolore sit consequuntur repellendus deleniti mollitia dolores possimus, tempore, ipsum in nesciunt odit praesentium veritatis explicabo harum, odio maxime hic eveniet! Id, asperiores aliquid laborum dolores, eligendi ut recusandae vitae minus doloremque voluptas porro voluptate sapiente sed nihil vel error architecto incidunt consectetur qui delectus sequi molestias cupiditate. Distinctio necessitatibus mollitia vitae amet sit voluptate velit aut adipisci nihil obcaecati? Porro error, vel maxime id minus quasi! Cupiditate amet modi, placeat quis delectus enim ea neque laudantium corporis eius officia, dignissimos iste, nemo recusandae officiis omnis hic pariatur? Laboriosam quos et saepe."            
        ]
    ];

    public static function all()
    {
        return collect(self::$blog_posts);
    }

    public static function find($slug)
    {
        $posts = static::all();
        return $posts->firstWhere('slug', $slug);
    }
}
