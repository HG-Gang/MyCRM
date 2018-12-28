<?php
/**
 * Created by PhpStorm.
 * User: I5
 * Date: 2018/12/26
 * Time: 15:18
 */

namespace App\Http\Controllers\admin;


use App\Http\Controllers\CommonController\Abstract_Mt4service_Controller;
use App\Model\DepositRecordLog;
use Illuminate\Http\Request;

class UnDepositAmountController extends Abstract_Mt4service_Controller
{
    public function undeposit_flow ()
    {
        return view('admin.undeposit_flow.undeposit_flow_browse');
    }

    public function undepositFlowSearch (Request $request)
    {
        $result = array('rows' => '', 'total' => '', 'footer' => '');

        $data   = array(
            'undeposit_id'      => $request->undeposit_id,
            'userId'            => $request->userId,
            'deposit_startdate' => $request->deposit_startdate,
            'deposit_enddate'   => $request->deposit_enddate,
        );

        $_rs = $this->get_all_undeposit_list ('page', $data);

        if (!empty($_rs)) {
            //重新整理rs 结果集，将实际支付金额加进来，只有入金才有实际金额，其他均为0.00
            $result['rows'] = $_rs;
            $result['total'] = $this->get_all_undeposit_list ('count', $data);
        }

        return json_encode ($result);
    }

    protected function get_all_undeposit_list($totalType, $data)
    {
        $query_sql = DepositRecordLog::where('dep_status', '01')->where('voided', '01')
            ->where(function ($subWhere) use ($data) {
                if (!empty($data['deposit_startdate']) && !empty($data['deposit_enddate']) && $this->_exte_is_Date ($data['deposit_startdate']) && $this->_exte_is_Date ($data['deposit_enddate'])) {
                    $subWhere->whereBetween('deposit_record_log.rec_crt_date', [$data['deposit_startdate'] .' 00:00:00', $data['deposit_enddate'] . ' 23:59:59']);
            } else {
                    if(!empty($data['deposit_startdate']) && $this->_exte_is_Date ($data['deposit_startdate'])) {
                        $subWhere->where('deposit_record_log.rec_crt_date',  '>=', $data['deposit_startdate'] .' 23:59:59');
                    }
                    if(!empty($data['deposit_enddate']) && $this->_exte_is_Date ($data['deposit_startdate'])) {
                        $subWhere->where('deposit_record_log.rec_crt_date', '<', $data['deposit_enddate'] .' 00:00:00');
                    }
                }

                if (!empty($data['userId'])) {
                    $subWhere->where('deposit_record_log.rec_crt_user', $data['userId']);
                }
                if (!empty($data['undeposit_id'])) {
                    $subWhere->where('deposit_record_log.dep_outChannelNo', 'like', '%' . $data['undeposit_id'] . '%')->orWhere('deposit_record_log.dep_outTrande', 'like', '%' . $data['undeposit_id'] . '%');
                }
            });

        return $this->_exte_get_query_sql_data($query_sql, $totalType, 'rec_crt_date');
    }
}