{{ $view->headLink() }}
{{ $view->headScript() }}

<div id="wobs_calendar_{{$rand_int}}" class="wobs-calendar wobs-calendar-full"></div>

<script>

function formatDate(date)
{
    var s = '';
    var yyyy, mm, dd;

    yyyy = date.getFullYear();
    mm = date.getMonth()+1;
    mm = mm.toString();
    dd = date.getDate();
    dd = dd.toString();

    if (mm.length === 1) {
        mm = "0"+mm;
    }
    if (dd.length === 1) {
        dd = "0"+dd;
    }

    s = yyyy + "-" + mm + "-" + dd;
    return s;
}

function getArticlesOfTheDay( start, end, callback )
{
    var search_start, search_end;

    search_start = formatDate(start);
    search_end = formatDate(end);

    $.post("{{$view->baseUrl('/articleoftheday/article-of-the-day')}}",
        {"format": "json", "start": search_start, "end": search_end, "image_width": {{$imageWidth}}},
        function(data){
            callback(data.articles);
        },
        "json"
    );
}

$("#wobs_calendar_{{$rand_int}}").wobscalendar({
    'today': new Date('{{$today[0]}}', '{{$today[1]-1}}', '{{$today[2]}}'),
    'date': new Date('{{$year}}', '{{$month}}'),
    'articles': getArticlesOfTheDay,
    'defaultView': '{{$defaultView}}',
    'firstDay': {{$firstDay}},
    'navigation': {{if $nav}} true {{else}} false {{/if}},
    'showDayNames': {{if $dayNames}} true {{else}} false {{/if}},
    'earliestMonth': {{if $earliestMonth}} new Date('{{$earliestMonth[0]}}', '{{$earliestMonth[1]-1}}') {{else}} undefined {{/if}},
    'latestMonth': {{if $latestMonth}} new Date('{{$latestMonth[0]}}', '{{$latestMonth[1]-1}}') {{else}} undefined {{/if}},
    'monthNames': ['{{$view->translate('January')}}', '{{$view->translate('February')}}', '{{$view->translate('March')}}', '{{$view->translate('April')}}',
                    '{{$view->translate('May')}}', '{{$view->translate('June')}}', '{{$view->translate('July')}}', '{{$view->translate('August')}}',
                    '{{$view->translate('September')}}', '{{$view->translate('October')}}', '{{$view->translate('November')}}', '{{$view->translate('December')}}']
});
</script>