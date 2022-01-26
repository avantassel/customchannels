#!/bin/bash
make
make install
chmod +x ./build/bin/cmusic
./build/bin/cmusic play stream