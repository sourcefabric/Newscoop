{{ dynamic }}
Dear {{ $user_title }} {{ $user_name }},\n\n
This is an automatically generated mail message.\n\n
Your {{ $subs_type }} subscription (started on {{ $subs_date  }}) to publication "{{ $publication_name }}" {{ if $subs_expire }}will expire on {{ $subs_expire_date }} (in {{ $subs_remained_days }} days).{{ else }}{{ if $subs_expire_plan }} will expire as follows:{{ /if }}\n
{{ $expire_plan }} on {{ $subs_expire_date }} (remained {{ $subs_remained_days }}) - started on {{ $subs_start_date }}{{ /if }}\n\n
Please enter the site http://{{ $site }} to update subscription.\n
{{ /dynamic }}