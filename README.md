# 7zPHP
Simple implementation of compression and decompression based on 7zip

Archives:

+[DIR]bin

 [FILE]7zPHP.php

 [FILE]README.md

- - - - - - - - - - - - - - -

-[DIR]bin

       |

       ...[FILE]7zPHP.exe

       |

       ...[FILE]license.txt

       |

       ...[FILE]readme.txt

[FILE]7zPHP.php

[FILE]README.md



- - - - - - - - - - - - - - - 

HOW TO USE:



for compression:



1)

$object = new PHP7z();

$signzip->setSource('C:\Users\Owner\Documents\file.txt');

$signzip->compress();



OR





2)

$object = new PHP7z();

$signzip->setSource('C:\Users\Owner\Documents\file.txt');

$signzip->setDest('C:\Users\Owner\file.7z');

$signzip->compress();



OR





3)

$object = new PHP7z();

$signzip->setDebug(true);

$signzip->setSource('C:\Users\Owner\Documents\file.txt');

$signzip->setPass('password');

$signzip->compress();



OR



4)

$object = new PHP7z();

$signzip->setDebug(true);

$signzip->setSource('C:\Users\Owner\Documents\file.txt');

$signzip->setDest('C:\Users\Owner\Documents\file.7z');

$signzip->setPass('password');

$signzip->compress();
