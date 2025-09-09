<?php
// ImageFactory.php
namespace Database\Factories;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    protected $model = Image::class;
    
    private $fixedImages = [
        'https://olimpica.vtexassets.com/arquivos/ids/626184/7702231000028.jpg?v=637626540280270000',
        'https://themysteryshack.com/cdn/shop/products/bgcshirtflatsq_630x.jpg?v=1631167230',
        'https://m.media-amazon.com/images/I/B1pppR4gVKL._CLa%7C2140%2C2000%7C810-f2OFVFL.png%7C0%2C0%2C2140%2C2000%2B0.0%2C0.0%2C2140.0%2C2000.0_UY1000_.png',
        'https://d2zia2w5autnlg.cloudfront.net/97763/64f5082ed7cfa-large',
        'https://pbs.twimg.com/media/DZucXdNWsAIg1Ts.jpg',
        'https://media.vandal.net/i/1280x720/10-2023/18/2023101816333010_3.jpg',
        'https://i.pinimg.com/736x/51/44/21/514421884c96b7db1763d65049f6a362.jpg',
        'https://indiehoy.com/wp-content/uploads/2022/11/the-last-of-us.jpg',
        'https://techland.time.com/wp-content/uploads/sites/15/2012/11/halo-4-cortana.jpg?w=720&h=480&crop=1',
        'https://sm.ign.com/ign_es/screenshot/default/halo-infinite_u38n.jpg',
        'https://www.rollingstone.com/wp-content/uploads/2019/08/stevenuniverse.jpg?w=1581&h=1054&crop=1',
        'https://www.jardineriadomenech.com/wp-content/uploads/2023/10/tipos-de-decks-de-madera-para-jardines.jpg'
    ];
    
    public function definition(): array
    {
        return [
            'url' => $this->faker->randomElement($this->fixedImages),
        ];
    }
}