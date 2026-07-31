#ifndef ZSTD_MIMETYPE_EXCLUDE

// Image types listed separately, because svg is quite
// common and compresses well.
#define ZSTD_MIMETYPE_EXCLUDE "\
video/*, \
audio/*, \
image/png, \
image/gif, \
image/jpeg, \
image/jxl, \
image/jp2, \
image/jpm, \
image/webp, \
image/avif, \
image/vnd.microsoft.icon, \
image/x-icon, \
font/woff, application/font-woff, \
font/woff2, \
application/pdf, application/x-pdf, \
application/zip, application/x-zip-compressed, \
application/7z-compressed, application/x-7z-compressed, \
application/vnd.rar, application/x-vnd.rar, \
application/gzip, application/x-gzip, \
application/zstd, application/x-zstd, \
application/br, application/x+br, \
application/lz4, application/x-lz4, \
application/bzip2, application/x-bzip2\
"

#endif
