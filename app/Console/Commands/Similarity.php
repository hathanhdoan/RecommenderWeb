<?php

namespace App\Console\Commands;

use App\Comment;
use Illuminate\Console\Command;

class Similarity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sim';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo_now('getting res_ids.....');
        $res_ids = Comment::distinct()->where('AvgRating', '!=', 0)->orderBy('ResId', 'ASC')->pluck('ResId')->toArray();

        $res_ids_flip = array_flip($res_ids);
        echo_now('getting owner_ids......');
        $owner_ids = Comment::distinct()->where('AvgRating', '!=', 0)->orderBy('Owner_id', 'ASC')->pluck('Owner_id')->toArray();

//        echo $owner_ids[1169].' '.$owner_ids[9437]. 'Res_id[0]: '. $res_ids[0]. 'res_id[1]'. $res_ids[1];
//        return 1;
        $owner_ids_flip = array_flip($owner_ids);
        echo_now('getting comments.....');
        $comments = Comment::where('AvgRating', '!=', 0)->orderBy('Owner_id', 'ASC')->get()->toArray();

        $rating_matrix = array_fill(0, count($owner_ids), array_fill(0, count($res_ids), '-'));

        echo_now('Building rating matrix....  ');
        foreach ($comments as $key => $comment) {
            $x = $owner_ids_flip[$comment['Owner_id']];
            $y = $res_ids_flip[$comment['ResId']];

            $rating_matrix[$x][$y] = $comment['AvgRating'];

        }

        echo_now('normalizing....  ');

        foreach ($rating_matrix as $key => $owner_row) {
            $rating_matrix[$key] = normalizing($owner_row);
        }

        echo_now('Building similarity matrix.....  ');

//        $similarity_matrix = array_fill(0,count($res_ids), array_fill(0,count($res_ids),'-'));
        $data_create_sim = [];
        $start = time();
        for ($i = 0; $i < count($res_ids) - 1; $i++) {
            echo_now($i);
            for ($j = $i + 1; $j < count($res_ids); $j++) {
                $rating_of_res_A = array_column($rating_matrix, $i);
                $rating_of_res_B = array_column($rating_matrix, $j);

                $overlap_user_matrix = array_filter($rating_of_res_A, function ($val, $key) use ($rating_of_res_B) {
                    return ($val != '-') && ($rating_of_res_B[$key] != '-');
                }, ARRAY_FILTER_USE_BOTH);


                if (count($overlap_user_matrix) > 20) {
                    //calculate similary
                    $numerator = 0;// tử số
                    $denominator1 = 0;// mẫu số
                    $denominator2 = 0;// mẫu số
                    foreach ($overlap_user_matrix as $key_ovl => $val_ovl) {
                        $numerator = $numerator + ($val_ovl * $rating_of_res_B[$key_ovl]);
                        $denominator1 = $denominator1 + pow($val_ovl, 2);
                        $denominator2 = $denominator2 + pow($rating_of_res_B[$key_ovl], 2);
                    }
                    $sim = $numerator / (sqrt($denominator1) * sqrt($denominator2));
                    $data_create = [
                        "res_source" => $res_ids[$i],
                        "res_destination" => $res_ids[$j],
                        "sim" => $sim
                    ];
                    $data_create_sim[] = $data_create;
                }
            }
        }
        $end = time();
        \App\Similarity::insert($data_create_sim);
        echo_now('Thanh cong: '.$end-$start);
        return 1;
    }
}
