<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardBoardData extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table      = 'board_board_datas';
    protected $guarded    = [];
    protected $fillable   = [];

    public function categories()
    {
        return $this->belongsToMany(BoardCategoryData::class, 'board_category_mappings', 'board_id', 'category_id');
    }

    public function admin_user()
    {
        return $this->hasOne(AdminData::class, "user_id", "user_id");
    }

    public function replies()
    {
        return $this->hasMany(BoardReplyData::class, 'board_id', 'id')->orderBy("created_at", "desc");
    }
}
