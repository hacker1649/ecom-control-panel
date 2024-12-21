<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class ReportController extends Controller
{
  /**
   * Fetch report data for the given year.
   */
  private function fetchReportData($yearQuery)
  {
    // Fetch all categories
    $allCategories = DB::table('category')
      ->select('c_id', 'c_name AS category_name')
      ->get();

    // Fetch report data for the selected year
    $reportData = DB::table('category AS cat')
      ->leftJoin('product AS p', 'cat.c_id', '=', 'p.c_id')
      ->leftJoin('cart_item AS ci', 'ci.p_id', '=', 'p.p_id')
      ->leftJoin('cart AS c', function ($join) use ($yearQuery) {
        $join->on('ci.c_id', '=', 'c.c_id')
          ->where('c.c_status', 3)
          ->whereYear(DB::raw('FROM_UNIXTIME(c.created_on)'), '=', $yearQuery);
      })
      ->select(
        'cat.c_id AS category_id',
        'cat.c_name AS category_name',
        DB::raw("SUM(CASE WHEN DATE_FORMAT(FROM_UNIXTIME(c.created_on), '%m') = '01' THEN 1 ELSE 0 END) AS January"),
        DB::raw("SUM(CASE WHEN DATE_FORMAT(FROM_UNIXTIME(c.created_on), '%m') = '02' THEN 1 ELSE 0 END) AS February"),
        DB::raw("SUM(CASE WHEN DATE_FORMAT(FROM_UNIXTIME(c.created_on), '%m') = '03' THEN 1 ELSE 0 END) AS March"),
        DB::raw("SUM(CASE WHEN DATE_FORMAT(FROM_UNIXTIME(c.created_on), '%m') = '04' THEN 1 ELSE 0 END) AS April"),
        DB::raw("SUM(CASE WHEN DATE_FORMAT(FROM_UNIXTIME(c.created_on), '%m') = '05' THEN 1 ELSE 0 END) AS May"),
        DB::raw("SUM(CASE WHEN DATE_FORMAT(FROM_UNIXTIME(c.created_on), '%m') = '06' THEN 1 ELSE 0 END) AS June"),
        DB::raw("SUM(CASE WHEN DATE_FORMAT(FROM_UNIXTIME(c.created_on), '%m') = '07' THEN 1 ELSE 0 END) AS July"),
        DB::raw("SUM(CASE WHEN DATE_FORMAT(FROM_UNIXTIME(c.created_on), '%m') = '08' THEN 1 ELSE 0 END) AS August"),
        DB::raw("SUM(CASE WHEN DATE_FORMAT(FROM_UNIXTIME(c.created_on), '%m') = '09' THEN 1 ELSE 0 END) AS September"),
        DB::raw("SUM(CASE WHEN DATE_FORMAT(FROM_UNIXTIME(c.created_on), '%m') = '10' THEN 1 ELSE 0 END) AS October"),
        DB::raw("SUM(CASE WHEN DATE_FORMAT(FROM_UNIXTIME(c.created_on), '%m') = '11' THEN 1 ELSE 0 END) AS November"),
        DB::raw("SUM(CASE WHEN DATE_FORMAT(FROM_UNIXTIME(c.created_on), '%m') = '12' THEN 1 ELSE 0 END) AS December")
      )
      ->groupBy('cat.c_id', 'cat.c_name')
      ->orderBy('cat.c_id')
      ->get();

    // Prepare structured data
    $structuredData = [];
    foreach ($allCategories as $category) {
      $structuredData[$category->category_name] = array_fill(1, 12, 0); // Default values for all months
    }

    foreach ($reportData as $row) {
      $structuredData[$row->category_name] = [
        1 => $row->January,
        2 => $row->February,
        3 => $row->March,
        4 => $row->April,
        5 => $row->May,
        6 => $row->June,
        7 => $row->July,
        8 => $row->August,
        9 => $row->September,
        10 => $row->October,
        11 => $row->November,
        12 => $row->December,
      ];
    }

    return [
      'structuredData' => $structuredData,
      'categories' => $allCategories->pluck('category_name')->toArray(),
    ];
  }

  /**
   * Display the monthly category report.
   */
  public function index(Request $request)
  {
    $yearQuery = $request->input('year', now()->year);

    $report = $this->fetchReportData($yearQuery);

    return view('admin.generate_report', [
      'structuredData' => $report['structuredData'],
      'categories' => $report['categories'],
      'yearQuery' => $yearQuery,
    ]);
  }

  /**
   * Generate the monthly category report as a PDF.
   */
  public function generatePdf(Request $request)
  {
    $yearQuery = $request->input('year', now()->year);

    $report = $this->fetchReportData($yearQuery);

    $html = view('admin.print_report', [
      'structuredData' => $report['structuredData'],
      'categories' => $report['categories'],
      'yearQuery' => $yearQuery,
    ])->render();

    $mpdf = new Mpdf();
    $mpdf->WriteHTML($html);

    return $mpdf->Output('report_' . $yearQuery . '.pdf', 'I');
  }
}
