<?php

# database/seeds/PageTableSeeder.php

use App\Models\Page;  
use Illuminate\Database\Seeder;

class PageTableSeeder extends Seeder  
{
    public function run()
    {
        Page::create([
            'title' => 'Title One',
            'slug' => 'title-one',
            'body' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris tellus odio, luctus at elit posuere, porta consequat orci. Nullam tempus efficitur lacus vel ullamcorper. Mauris rutrum pellentesque urna, sed mattis lorem pellentesque vitae. Maecenas ac nibh sed augue porta luctus. Cras in elit mollis, suscipit arcu eget, porta ipsum. Fusce lorem mi, pretium nec tincidunt non, bibendum ac lectus. Donec vel tempus mauris, sed scelerisque justo.</p><p>Curabitur egestas suscipit varius. Quisque eget elit efficitur, aliquet mauris quis, tincidunt mi. Sed laoreet massa at porttitor maximus. Curabitur ante metus, ornare sit amet ligula eu, cursus sollicitudin lectus. Pellentesque quis augue nunc. Phasellus pulvinar nisi sed metus semper ullamcorper. Fusce commodo euismod purus ut iaculis. Morbi in neque libero. Integer magna erat, sagittis a quam vitae, egestas placerat quam. Suspendisse consequat ultricies augue ac consequat. Sed turpis massa, consequat ac nisi vel, placerat elementum magna. Sed ultrices turpis a sagittis faucibus. Fusce diam nunc, condimentum a mattis quis, consequat ac sem. Integer ornare congue quam. Nulla varius metus quis fermentum mollis.</p>'
        ]);

        Page::create([
            'title' => 'Title Two',
            'slug' => 'title-two',
            'body' => '<p>Proin a iaculis tortor. Suspendisse malesuada velit a purus vehicula, quis semper tellus gravida. Donec placerat finibus rutrum. Aenean hendrerit, nisi a porta feugiat, magna sem laoreet ex, a accumsan ipsum lacus ut nisi. Nullam volutpat commodo dictum. Ut dapibus enim eros, ut tincidunt urna dignissim quis. Mauris euismod eros orci, ac commodo nulla finibus eget. Pellentesque efficitur convallis malesuada. Nullam laoreet quam vitae quam convallis maximus. Maecenas convallis justo a est euismod, vitae bibendum sem hendrerit.</p><p>Mauris in leo ligula. Mauris at diam a augue posuere facilisis. Praesent a lectus ut turpis congue vulputate vitae id dolor. Nulla bibendum nulla at lobortis molestie. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vestibulum mollis odio ac massa viverra, at interdum magna bibendum. Suspendisse potenti. Proin ac orci scelerisque, ultrices lacus id, rhoncus eros. Donec laoreet quam eget ligula luctus dapibus. Vivamus laoreet ornare leo a facilisis. Phasellus eleifend eget mauris id sagittis. In consectetur, leo nec sagittis tempus, ante nisl ullamcorper dui, rhoncus vehicula leo mi vitae quam. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>'
        ]);

        Page::create([
            'title' => 'Title Three',
            'slug' => 'title-three',
            'body' => '<p>Etiam nec velit dolor. Sed eget elit sit amet odio mattis euismod. Suspendisse potenti. Fusce finibus lacus in augue rutrum aliquet. Nunc at vulputate neque, et commodo neque. Donec dictum, sem sit amet tristique fermentum, massa metus ullamcorper nibh, sit amet vulputate justo mauris in felis. Pellentesque maximus lectus a venenatis porttitor. Cras id lobortis risus, sit amet bibendum nunc.</p>'
        ]);

        //... add more quotes if you want!
    }
}