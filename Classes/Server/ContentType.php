<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server;

/**
 * Content types taken from http://en.wikipedia.org/wiki/Internet_media_type#List_of_common_media_types
 */
interface ContentType
{
    const CSS_TEXT = 'text/css';
    const CSV_TEXT = 'text/csv';
    const HTML_TEXT = 'text/html';
    const PLAIN_TEXT = 'text/plain';
    const XML_TEXT = 'text/xml';
    const RTF_TEXT = 'text/rtf';
    const VCARD_TEXT = 'text/vcard';

    const AVI_VIDEO = 'video/avi';
    const MPEG_VIDEO = 'video/mpeg';
    const MP4_VIDEO = 'video/mp4';
    const OGG_VIDEO = 'video/ogg';
    const QUICKTIME_VIDEO = 'video/quicktime';
    const WEBM_VIDEO = 'video/webm';
    const MATROSKA_VIDEO = 'video/x-matroska';
    const WMV_VIDEO = 'video/x-ms-wmv';
    const FLV_VIDEO = 'video/x-flv';

    const GIF_IMAGE = 'image/gif';
    const JPEG_IMAGE = 'image/jpeg';
    const PJPEG_IMAGE = 'image/pjpeg';
    const PNG_IMAGE = 'image/png';
    const SVG_XML_IMAGE = 'image/svg+xml';
    const VND_DJVU_IMAGE = 'image/vnd.djvu';


    const IGES_MODEL = 'model/iges';
    const MESH_MODEL = 'model/mesh';
    const VRML_MODEL = 'model/vrml';
    const X3D_BINARY_MODEL = 'model/x3d+binary';
    const X3D_FASTINFOSET_MODEL = 'model/x3d+fastinfoset';
    const X3D_VRML_MODEL = 'model/x3d-vrml';
    const X3D_XML_MODEL = 'model/x3d+xml';
    const EXAMPLE_MODEL = 'model/example';

    const BASIC_AUDIO = 'audio/basic';
    const L24_AUDIO = 'audio/L24';
    const MP4_AUDIO = 'audio/mp4';
    const MPEG_AUDIO = 'audio/mpeg';
    const OGG_AUDIO = 'audio/ogg';
    const OPUS_AUDIO = 'audio/opus';
    const VORBIS_AUDIO = 'audio/vorbis';
    const VND_RN_REALAUDIO_AUDIO = 'audio/vnd.rn-realaudio';
    const VND_WAVE_AUDIO = 'audio/vnd.wave';
    const WEBM_AUDIO = 'audio/webm';
    const EXAMPLE_AUDIO = 'audio/example';


    const ATOM_XML_APPLICATION = 'application/atom+xml';
    const DART_APPLICATION = 'application/dart';
    const ECMASCRIPT_APPLICATION = 'application/ecmascript';
    const EDI_X12_APPLICATION = 'application/EDI-X12';
    const EDIFACT_APPLICATION = 'application/EDIFACT';
    const JSON_APPLICATION = 'application/json';
    const JAVASCRIPT_APPLICATION = 'application/javascript';
    const OCTET_STREAM_APPLICATION = 'application/octet-stream';
    const OGG_APPLICATION = 'application/ogg';
    const PDF_APPLICATION = 'application/pdf';
    const POSTSCRIPT_APPLICATION = 'application/postscript';
    const RDF_XML_APPLICATION = 'application/rdf+xml';
    const RSS_XML_APPLICATION = 'application/rss+xml';
    const SOAP_XML_APPLICATION = 'application/soap+xml';
    const FONT_WOFF_APPLICATION = 'application/font-woff';
    const XHTML_XML_APPLICATION = 'application/xhtml+xml';
    const XML_APPLICATION = 'application/xml';
    const XML_DTD_APPLICATION = 'application/xml-dtd';
    const XOP_XML_APPLICATION = 'application/xop+xml';
    const ZIP_APPLICATION = 'application/zip';
    const GZIP_APPLICATION = 'application/gzip';
    const EXAMPLE_APPLICATION = 'application/example';
    const NACL_APPLICATION = 'application/x-nacl';
    const PNACL_APPLICATION = 'application/x-pnacl';
}