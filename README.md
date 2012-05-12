swPhpThumber
============

A very simple script to automate and distribute thumbmail generator writtern in PHP.

The idea is to have the ability to detach the thumbnail generation process
from existing webapplications by providing a very simple Rest API.

The following are some examples that will work:

http://yoursite.com/images/someimage.jpg?w=200&h=100

This will return a 200x100 version of the someimage.jpg file. No scaling will
be perforemed on someimage.jpg.

http://yoursite.com/images/someimage.jpg?w=200

This will return a rescales version of someimage.jpg with a maximum width of 200x

http://yoursite.com/images/someimage.jpg?h=100

Same as before but the returned image will have 100px of height.

http://yoursite.com/images/someimage.jpg?w=200&scale=no

This will return a 200xOriginalHeight version of the someimage.jpg file, no
scaling will be applied.


Some features that will be added in the future:

1. Support of Gearman, further improving your ability to detach and enqueue
your thumb generation process.

2. Caching configuration via url/local files, this will allow you to indicate
wether you want to force the thumb regeneration or if you are ok with getting
a new version of the image.

3. Inline image generation to embed the image directly into css/html files
reducing the ammount of requests.
