<?php

namespace App\Services\Business;

use App\Models\Diary\Diary;

class DiaryService extends BaseService
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加编辑日记
     * @param $params
     * @return Diary|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function editDiary($params)
    {
        $uid     = $params['uid'];
        $diaryId = $params['diary_id'] ?? 0;
        $diary   = Diary::query()->where('uid', $uid)->where('diary_id', $diaryId)->first();
        if ( ! $diary) {
            $diary       = new Diary();
            $diary->uid  = $uid;
            $diary->date = date('Ymd');
        }
        $diary->title   = $params['title'];
        $diary->content = $params['content'];
        $diary->app_id  = $params['app_id'] ?? BaseService::WEB_ID;
        $diary->save();

        return $diary;
    }

    /**
     * 获取日记列表
     * @param $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDiaryList($params)
    {
        $uid          = $params['uid'];
        $getDiaryList = Diary::query()->where('uid', $uid)->where('is_delete', 0)->paginate(15);

        return $getDiaryList;
    }

    public function delDiary($params)
    {
        $uid     = $params['uid'];
        $diaryId = $params['diary_id'] ?? 0;
        Diary::query()->where('uid', $uid)->where('diary_id', $diaryId)->update(['is_delete' => 1]);

        return '';
    }

}