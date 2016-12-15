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
           $('#citySearch').devbridgeAutocomplete({
               serviceUrl: "http://" + server_name + "/api/citycountry",
               dataType: 'jsonp',
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
                           //console.log(response);
                           //console.log('item' + item);
                           return { value: item.city_name, data: item.subdivision_2_name + ' ' + item.subdivision_1_name + ' ' + item.country_name };
                       })
                   };
                },
                formatResult: function(suggestion, currentValue){
                    return suggestion.value + ', ' + suggestion.data;
                },
               onSelect: function (suggestion) {
                   console.log(suggestion.value + ' ' + suggestion.data);
                   $("#selection").html(suggestion.value + ', ' + suggestion.data);
                   LatLon(suggestion.value + ' ' + suggestion.data);
               },
               onSearchError: function (query, jqXHR, textStatus, errorThrown) {
                   $('#citySearch').removeClass('autocomplete-loading');
                   console.log("textStatus: " + textStatus);
                   console.log(query);
               }
           });
       });

       function LatLon(city) {
           console.log(city);
          $.ajax({
              url: "http://" + server_name + "/api/latlon?city=" + city,
              success: function(data) {
                  console.log(data);
              }
          });
       };

    });
    </script>
</body>
</html>
