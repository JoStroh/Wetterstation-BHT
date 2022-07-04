//Quelle: https://dyclassroom.com/chartjs/chartjs-how-to-create-line-graph-using-data-from-mysql-mariadb-table-and-php
//Quelle: https://www.chartjs.org/docs/latest/

// Warten auf den Seitenaufbau
$(document).ready(function() {});

// Definition der Ajax-Funktion
$.ajax({
url : "/PHP/Feuchtigkeit.php",
type : "GET",
// Abfrage der Daten von der PHP-Seite
success : function(data){
console.log(data);

// Identifizieren des HTML-Canvas-Ojektes mittels seiner ID
const Druck_Chart_obj = document.getElementById('Hum_Chart').getContext("2d");

// Definition des Diagramms 
let chart = new Chart(Druck_Chart_obj,{
    type: 'line',
    data:{
// Setzen der Datenquelle          
        datasets:[{
            label:"Luftfeuchtigkeit",
            fill: true,
            backgroundColor: '#28baa280',
            data:data
        }]
    },
// bestimmen der x und y Werte anhand der Namen der Objektklassen      
    options: {
        parsing:{
            xAxisKey: 'StatusTime',
            yAxisKey: 'Hum'
        },
        plugins: {
            legend: {
                display: false
            },
// Definition der Zoom-Biblothek             
            zoom:{
                limits: {y:{min:0},},
                pan: {
                    enabled: true,
                    mode: 'xy',
                   }, 
                zoom:{
                    wheel:{
                        enabled: true
                    },
                    pinch: {
                        enabled: true
                    },
                    mode: 'xy'    
               }
            }
        },
// Definition der Datenpunktdarstellung          
        elements: {
            point:{
                radius: 0,
                hitRadius: 5,
                hoverRadius: 5,
            }  
        },
// Definition der Achsen sowie deren Bezeichnung         
        scales:{
            y:{
                ticks: {
                    callback: function(value, index, ticks) {
                        return value.toFixed(2) + " %";
                    }
                },
                grid: {
                    display: true,
                    color: '#7777771A'
                }
            },
            X: {
                type:'time',
                time:{
                    unit: 'minute',
                    displayFormats: {'minute': 'HH:mm/DD.MM'},
                    tooltipFormat: 'DD.MM.YY HH:mm', 
                },
                ticks: {
                    maxTicksLimit: 10
                },               
                grid: {
                    display: true,
                    color: '#7777771A'
                }
            }
        }
    },

});
// Definition der Funktion für das Zurücksetzen der des Diagrammausschnitts
$('#reset_zoom').click(function(){
    chart.resetZoom();
}); 
},

// Verhalten der Ajax-Funktion bei einem fehlgeschlagenen Datenabruf
error : function(data) {
console.log(data);
}
});

    