
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.core.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/TableExport/5.2.0/js/tableexport.min.js"></script>

<script type="text/javaScript">

    function doExport(selector, params) {
      const options = {
        tableName: 'Table name'
      };

      jQuery.extend(true, options, params);

      $(selector).tableExport(options);

      var css_property =
      {
          "position": "fixed",
          "top": "10px",
          "right": "20px",
          "padding": "5px 15px",
          "border-radius": "10px",
          "border": "0.5px solid lightgray",
          "background-color": "#fff",
          "font-weight": "bold",
          "font-family": "sans-serif",
          "font-size": "16px",
      }

      $('.button-default').css(css_property).text('Csv');
    }

    $(function () {
        doExport('#table_data', {
            headings: true, // (Boolean), display table headings (th/td elements) in the <thead>
            footers: true, // (Boolean), display table footers (th/td elements) in the <tfoot>
            formats: ["csv"], // (String[]), filetypes for the export
            fileName: "id", // (id, String), filename for the downloaded file
            exportButtons: true,
            position: "top", // (top, bottom), position of the caption element relative to table
            ignoreRows: null, // (Number, Number[]), row indices to exclude from the exported files
            ignoreCols: null, // (Number, Number[]), column indices to exclude from the exported file(s)
            ignoreCSS: ".tableexport-ignore", // (selector, selector[]), selector(s) to exclude from the exported file(s)
            emptyCSS: ".tableexport-empty", // (selector, selector[]), selector(s) to replace cells with an empty string in the exported file(s)
            trimWhitespace: true // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s)
        });
    });

  </script>


@if(request()->get('action') == 'excel')
    @php
        $name = request()->get('code') ? request()->get('code').'.xls' : 'excel.xls';
        header('Content-Type: application/force-download');
        header('Content-disposition: attachment; filename='.$name);
        header("Pragma: ");
        header("Cache-Control: ");
    @endphp
@else
    <div class="header-action">
        <nav>
            <a href="{{ moduleRoute('getCreate') }}">Back</a>
            <a class="cursor" onclick="window.print()">Print</a>
            <a href="{{ url()->full().'&action=excel' }}">Excel</a>
        </nav>
    </div>
@endif
