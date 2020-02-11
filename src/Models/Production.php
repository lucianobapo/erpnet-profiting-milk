<?php

namespace ErpNET\Profiting\Milk\Models;

use App\Models\Model;
use App\Models\Setting\Category;
use App\Traits\Currencies;
use App\Traits\DateTime;
use App\Traits\Media;
use App\Traits\Recurring;
use Bkwld\Cloner\Cloneable;
use Sofa\Eloquence\Eloquence;
use Date;

class Production extends Model
{
    use Cloneable, 
    //Currencies, 
    DateTime, Eloquence, Media, Recurring;

    protected $table = 'milk_productions';

    protected $dates = ['deleted_at', 'posted_at'];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 
        //'account_id', 
        'posted_at', 
        'quantity', 
        //'currency_code', 
        //'currency_rate', 
        'vendor_id', 
        'description', 
        'category_id', 
        //'payment_method', 
        'reference', 
        'parent_id',
        
    ];

    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = [
        'posted_at', 
        'quantity', 
        'category.name', 
        //'account.name',        
    ];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchableColumns = [
        //'accounts.name',
        'categories.name',
        'vendors.name' ,
        'description'  ,
    ];

    /**
     * Clonable relationships.
     *
     * @var array
     */
    public $cloneable_relations = ['recurring'];


    public function category()
    {
        return $this->belongsTo('App\Models\Setting\Category');
    }

    public function recurring()
    {
        return $this->morphOne('App\Models\Common\Recurring', 'recurable');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Expense\Vendor');
    }


    /**
     * Convert amount to double.
     *
     * @param  string  $value
     * @return void
     */
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = (double) $value;
    }

    /**
     * Convert currency rate to double.
     *
     * @param  string  $value
     * @return void
     
    public function setCurrencyRateAttribute($value)
    {
        $this->attributes['currency_rate'] = (double) $value;
    }*/

    public static function scopeLatest($query)
    {
        return $query->orderBy('posted_at', 'desc');
    }

    /**
     * Get the current balance.
     *
     * @return string
     */
    public function getAttachmentAttribute($value)
    {
        if (!empty($value) && !$this->hasMedia('attachment')) {
            return $value;
        } elseif (!$this->hasMedia('attachment')) {
            return false;
        }

        return $this->getMedia('attachment')->last();
    }
}
