{{ if $campsite->article->has_map }}
    {{ map show_locations_list="true"
           show_reset_link="Show initial Map" }}

    {{ if $campsite->language->name == "English" }}
    <p><strong>List of Locations Per Article:</strong></p>
    <p>By being able to fetch <i>longitude</i> and <i>latitude</i> data for each location
    on a map linked to an Article, you are able to do now cool stuff like creating GeoRSS files.</p>
    {{ else }}
    <p><strong>Lista de Puntos de inter&eacute;s Por Art&iacute;culo</strong></p>
    <p>Al ser posible acceder a los datos de <i>longitud</i> y <i>latitud</i> para cada
    punto de inter&eacute;s sobre un mapa vinculado a un Art&iacute;culo, es posible ahora
    crear cosas interesantes como archivos GeoRSS y conectar con proveedores de Servicios de
    Mapas en-l&iacute;nea.
    {{ /if }}
    <ul>
    {{ list_article_locations }}
        <li>{{ $campsite->location->name }} ({{ $campsite->location->longitude }}, {{ $campsite->location->latitude }})</li>
    {{ /list_article_locations }}
    </ul>
{{ /if }}