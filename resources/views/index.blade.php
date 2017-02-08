<!DOCTYPE html>
<html>
<head>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="js/jquery.autocomplete.js"></script>
    <style>
        .autocomplete-suggestions { border: 1px solid #999; background: #fff; cursor: default; overflow: auto; }
        .autocomplete-suggestion { padding: 10px 5px; font-size: 1em; white-space: nowrap; overflow: hidden; }
        .autocomplete-selected { background: #f0f0f0; }
        .autocomplete-suggestions strong { font-weight: normal; color: #3399ff; }
        .autocomplete-loading {
            background: white url('img/ajax-loader-big-roller.gif') right center no-repeat;
        }

        #citySearch {
            display: block;
            width: 25%;
            height: 25px;
            font-size: 1em;
            text-align: center;
            margin-bottom: 35px;
        }


    </style>
</head>

<body>
    <h1>Test City API</h1>

    <p id="selection">Nothing selected</p>

    <form id="citySearchForm">
        <input id="citySearch"  type="text" size="40" placeholder="City name..."/>
    </form>

    <script>
    $(document).ready(function() {
        var server_name = window.location.host;
       $(function() {
           if (location.protocol !== 'https') {
               console.log('http');
                var url = "http://" + server_name + "/api/citycountry";
            }
            else
                var url = "https://" + server_name + "/api/citycountry";
           $('#citySearch').devbridgeAutocomplete({
               serviceUrl: url,
               dataType: 'jsonp',
               jsonp: 'cb',
               paramName: 'key',
               autoSelectFirst: true,
               onSearchStart: function () {
                   $('#citySearch').addClass('autocomplete-loading');
               },
               onSearchComplete: function() {
                   $('#citySearch').removeClass('autocomplete-loading');
               },
               transformResult: function(response) {
                   console.log(response);
                   return {
                       suggestions: $.map(response, function(item) {
                           return { value: item.city_name, data: item.subdivision_1_name + ',' + item.country_name };
                       })
                   };
                },
                formatResult: function(suggestion, currentValue){
                    return suggestion.value + ', ' + suggestion.data;
                },
                onSelect: function (suggestion) {
                   console.log(suggestion.value + ' ' + suggestion.data);
                   $("#selection").html(suggestion.value + ', ' + suggestion.data);
                   LatLon(suggestion.value + ',' + suggestion.data);
                },
                onSearchError: function (query, jqXHR, textStatus, errorThrown) {
                   $('#citySearch').removeClass('autocomplete-loading');
                   console.log("textStatus: " + textStatus);
                   console.log(query);
                }
           });
       });

       function LatLon(city) {

           var areas = city.split(',');
           console.log(areas);
           var url = "http://" + server_name + "/api/latlon?city=" + areas[0] + '&subdivision1=' + areas[1] + '&country=' + areas[2];
           console.log(url);
           $.ajax({
                url: url,
                success: function(data) {
                   console.log(data);
                }
          });
       };

    });
    </script>
</body>
</html>
