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
        'https://www.jardineriadomenech.com/wp-content/uploads/2023/10/tipos-de-decks-de-madera-para-jardines.jpg',
        'https://i.pinimg.com/736x/76/a3/1b/76a31bca2d9f963b625cbcaf9a1e0537.jpg',
        'https://i.pinimg.com/1200x/b6/c4/cd/b6c4cde196e12850d64cc6570eef7674.jpg',
        'https://i.pinimg.com/736x/53/28/2f/53282fa3f2e64803319418a7524ad713.jpg',
        'https://i.pinimg.com/736x/6e/13/31/6e1331c8c0a4842b4d5f29d9d777d259.jpg',
        'https://i.pinimg.com/736x/b4/34/87/b43487a1bcd30650e4e760cada9598c2.jpg',
        'https://i.pinimg.com/736x/93/fd/11/93fd1154bc28d806af25faab8ee861a5.jpg',
        'https://i.pinimg.com/1200x/11/ef/d7/11efd75b7b3bb8fbfbbae1e582473691.jpg',
        'https://i.pinimg.com/736x/e5/ac/61/e5ac61ca28eafb7e9d3eedf9986d245a.jpg',
        'https://i.pinimg.com/736x/89/78/bd/8978bdbc55a90e878c543383eb5dffc4.jpg',
        'https://i.pinimg.com/736x/c5/8b/7a/c58b7a99c6e83c56411b5a97dfe00fce.jpg',
        'https://i.pinimg.com/736x/d3/92/85/d392856a1db26b39acf896f654034d34.jpg',
        'https://i.pinimg.com/736x/c6/76/bc/c676bcb365c2e0423c422da77b07f191.jpg',
        'https://i.pinimg.com/736x/ce/e8/f0/cee8f001e0ab5235a530681d84a7184b.jpg',
        'https://i.pinimg.com/736x/f0/d9/5c/f0d95c2d8931475584b8ef8b5b24d57c.jpg',
        'https://i.pinimg.com/736x/a3/9d/b0/a39db076b74c90a38f936875d06215eb.jpg',
        'https://i.pinimg.com/1200x/36/a8/8b/36a88b8316fc0fac706732578b427697.jpg',
        'https://i.pinimg.com/736x/ed/4a/e6/ed4ae677cb5591f91dcee518172d8c88.jpg',
        'https://i.pinimg.com/1200x/51/e3/c9/51e3c96102b4d29466bdec901847ee65.jpg',
        'https://i.pinimg.com/736x/96/47/8e/96478e35d1b19dee2938c2c583ee632e.jpg',
        'https://i.pinimg.com/736x/a4/b6/df/a4b6df71d43cfbe4c470024e9906f159.jpg',
        'https://i.pinimg.com/1200x/9c/07/b6/9c07b63c1d44a22104c19807a8a3a703.jpg',
        'https://i.pinimg.com/736x/20/6e/32/206e32e96dd918b67ee68ab8118612a4.jpg',
        'https://i.pinimg.com/736x/dc/27/ea/dc27eabcff23052000e9c99b14d02e10.jpg',
        'https://i.pinimg.com/1200x/c5/1d/ba/c51dba91460eb2e145bbf0e29bee8f42.jpg',
        'https://images7.memedroid.com/images/UPLOADED555/62c1103df10b7.jpeg',
        'https://media.cnn.com/api/v1/images/stellar/prod/cnne-212344-monkey-selfie.jpeg?c=16x9&q=h_833,w_1480,c_fill',
        'https://i.pinimg.com/736x/94/e1/14/94e1143ce4e6b89aa40b794d2599ad6b.jpg',
        'https://i.pinimg.com/736x/b0/a5/92/b0a592ef43007393f5f312559e7ae32f.jpg',
        'https://i.pinimg.com/736x/1a/76/7c/1a767c4572d016ba1c8f6d6363be5e23.jpg',
        'https://i.pinimg.com/736x/28/8a/74/288a74ca157de712c14b0ed6ad22e56b.jpg',
        'https://i.pinimg.com/736x/fd/26/5c/fd265ccd108181a75a5969c65c3e37a6.jpg',
        'https://i.pinimg.com/736x/b8/fa/22/b8fa222be2d0a0fb0bcdefc2d639548b.jpg',
        'https://i.pinimg.com/1200x/67/3e/d3/673ed36ec9fa28e0ba66543c4232e2e0.jpg',
        'https://i.pinimg.com/736x/e2/e7/d5/e2e7d509659e4b08ac8d4cb2a139df19.jpg',
        'https://i.pinimg.com/1200x/da/13/2a/da132a6af1b306745ab76e039cd6066d.jpg',
        'https://i.pinimg.com/736x/e9/17/2d/e9172d2c98648ba7af43aa6033454199.jpg',
        'https://i.pinimg.com/1200x/f6/a5/af/f6a5afee03f180a10ae2f63f99d112fd.jpg',
        'https://i.pinimg.com/736x/93/96/e2/9396e2cdf662eecabcc3183b6d18e631.jpg',
        'https://i.pinimg.com/736x/4e/f2/d2/4ef2d2985d6180f767f6a25328676032.jpg',
        'https://i.pinimg.com/736x/2a/10/00/2a1000444a2f43366b2b014ffd323757.jpg',
        'https://i.pinimg.com/736x/5e/6c/58/5e6c58274643c24034191b0112d8a2ec.jpg'


        
    ];
    
    public function definition(): array
    {
        return [
            'url' => $this->faker->randomElement($this->fixedImages),
        ];
    }
}