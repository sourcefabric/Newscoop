<?php
/**
 * @author $Author: holman $
 */

$videoFormat = array(
    '_root'=>'video',
    'video'=>array(
        'childs'=>array(
            'required'=>array('metadata'),
        ),
    ),
    'metadata'=>array(
        'childs'=>array(
            'required'=>array(
                'dc:title', 'dcterms:extent'
            ),
            'optional'=>array(
                'dc:identifier',
                'dc:creator', 'dc:source', 'dc:playtime_string',
                'ls:year', 'dc:type', 'dc:description', 'dc:format',
                'ls:audio_encoded_by', 'ls:composer', 'ls:url',
                'ls:bitrate', 'ls:video_bitrate', 'ls:audio_bitrate',
		'ls:audio_channels', 'ls:audio_samplerate', 'ls:audio_encoder',
		'ls:video_encoder', 'dc:title', 'dc:description',
                'dc:creator', 'dc:subject', 'dc:type', 'dc:format',
		'ls:video_total_frames', 'ls:video_frame_rate',
		'ls:video_frame_width',	'ls:video_frame_height',
		'ls:video_bgcolor',
                // extra
                'ls:filename', 'ls:filesize', 'ls:mtime',
            ),
        ),
        'namespaces'=>array(
            'dc'=>"http://purl.org/dc/elements/1.1/",
            'dcterms'=>"http://purl.org/dc/terms/",
            'xbmf'=>"http://www.streamonthefly.org/xbmf",
            'xsi'=>"http://www.w3.org/2001/XMLSchema-instance",
            'xml'=>"http://www.w3.org/XML/1998/namespace",
        ),
    ),
    'dc:identifier'=>array(
        'type'=>'Text',
        'auto'=>TRUE,
    ),
    'dc:title'=>array(
        'type'=>'Text',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dcterms:extent'=>array(
        'type'=>'Time',
//        'regexp'=>'^\d{2}:\d{2}:\d{2}.\d{6}$',
        'regexp'=>'^((\d{1,2}:)?\d{1,2}:)?\d{1,20}(.\d{1,6})?$',
    ),
    'dc:creator'=>array(
        'type'=>'Text',
        'area'=>'Video',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:source'=>array(
        'type'=>'Text',
        'area'=>'Audio',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:year'=>array(
        'type'=>'Menu',
        'area'=>'Video',
    ),
    'dc:type'=>array(
        'type'=>'Menu',
        'area'=>'Audio',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:description'=>array(
        'type'=>'Longtext',
        'area'=>'Video',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:format'=>array(
        'type'=>'Menu',
        'area'=>'Video',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:playtime_string'=>array(
	'type'=>'Text',
	'area'=>'Video',
	'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:audio_encoded_by'=>array(
        'type'=>'Text',
        'area'=>'Audio',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:composer'=>array(
        'type'=>'Text',
        'area'=>'Audio',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:bitrate'=>array(
        'type'=>'Number',
        'area'=>'Video',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:audio_bitrate'=>array(
        'type'=>'Number',
        'area'=>'Audio',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:audio_channels'=>array(
        'type'=>'Menu',
        'area'=>'Audio',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:audio_samplerate'=>array(
        'type'=>'Menu',
        'area'=>'Audio',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:audio_encoder'=>array(
        'type'=>'Text',
        'area'=>'Audio',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:video_bitrate'=>array(
        'type'=>'Number',
        'area'=>'Video',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:video_encoder'=>array(
        'type'=>'Text',
        'area'=>'Video',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:video_total_frames'=>array(
        'type'=>'Number',
        'area'=>'Video',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:video_frame_rate'=>array(
        'type'=>'Number',
        'area'=>'Video',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:video_frame_width'=>array(
        'type'=>'Number',
        'area'=>'Video',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:video_frame_height'=>array(
        'type'=>'Number',
        'area'=>'Video',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:video_frame_bgcolor'=>array(
        'type'=>'Text',
        'area'=>'Video',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'dc:description'=>array(
        'type'=>'Longtext',
        'area'=>'Video',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:filename'=>array(
        'type'=>'Text',
        'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:filesize'=>array(
	'type'=>'Int',
	'attrs'=>array('implied'=>array('xml:lang')),
    ),
    'ls:mtime'=>array(
        'type'=>'Int',
//        'regexp'=>'^\d{4}(-\d{2}(-\d{2}(T\d{2}:\d{2}(:\d{2}\.\d+)?(Z)|([\+\-]?\d{2}:\d{2}))?)?)?$',
    ),
);

?>
