<?php
return [
    'page_format'           => 'A4',
    'page_orientation'      => 'P',
    'page_units'            => 'mm',
    'unicode'               => true,
    'encoding'              => 'UTF-8',
    'font_directory'        => '',
    'image_directory'       => str_replace('/', DIRECTORY_SEPARATOR, public_path('images')).DIRECTORY_SEPARATOR,
    'tcpdf_throw_exception' => false,
    'use_fpdi'              => false,
    'use_original_header'   => false,
    'use_original_footer'   => false,
    'pdfa'                  => false, // Options: false, 1, 3

    // See more info at the tcpdf_config.php file in TCPDF (if you do not set this here, TCPDF will use it default)
    // https://raw.githubusercontent.com/tecnickcom/TCPDF/master/config/tcpdf_config.php

    //'path_main'           => '', // K_PATH_MAIN
    //'path_url'            => '', // K_PATH_URL
    'header_logo'         => 'logo_mello.png', // PDF_HEADER_LOGO
    'header_logo_width'   => 20, // PDF_HEADER_LOGO_WIDTH
    //    'path_cache'          => '', // K_PATH_CACHE
    //    'blank_image'         => '', // K_BLANK_IMAGE
    //    'creator'             => '', // PDF_CREATOR
    //    'author'              => '', // PDF_AUTHOR
    'header_title'        => "EPSSA S.A.C.", // PDF_HEADER_TITLE
    'header_string'       => "JR. Leoncio Prado N° 1802 - Tarapoto | Tel. 042 525 524 Cel. 942 021 961 - 942 606 411", // PDF_HEADER_STRING
    //    'page_units'          => '', // PDF_UNIT
    'margin_header'       => 5, // PDF_MARGIN_HEADER
    'margin_footer'       => 10, // PDF_MARGIN_FOOTER
    'margin_top'          => 27, // PDF_MARGIN_TOP
    'margin_bottom'       => 25, // PDF_MARGIN_BOTTOM
    'margin_left'         => 15, // PDF_MARGIN_LEFT
    'margin_right'        => 15, // PDF_MARGIN_RIGHT
    'font_name_main'      => 'helvetica', // PDF_FONT_NAME_MAIN
    'font_size_main'      => 10, // PDF_FONT_SIZE_MAIN
    'font_name_data'      => 'helvetica', // PDF_FONT_NAME_DATA
    'font_size_data'      => 8, // PDF_FONT_SIZE_DATA
    //    'foto_monospaced'     => '', // PDF_FONT_MONOSPACED
    'image_scale_ratio'   => 1.25, // PDF_IMAGE_SCALE_RATIO
    //    'head_magnification'  => '', // HEAD_MAGNIFICATION
    //    'cell_height_ratio'   => '', // K_CELL_HEIGHT_RATIO
    //    'title_magnification' => '', // K_TITLE_MAGNIFICATION
    //    'small_ratio'         => '', // K_SMALL_RATIO
    //    'thai_topchars'       => '', // K_THAI_TOPCHARS
    //    'tcpdf_calls_in_html' => '', // K_TCPDF_CALLS_IN_HTML
    //    'timezone'            => '', // K_TIMEZONE
    //    'allowed_tags'        => '', // K_ALLOWED_TCPDF_TAGS
];
