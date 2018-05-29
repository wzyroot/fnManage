<?php
namespace Landpush\Controller;
use Common\Controller\HomebaseController; 
class LandpushController extends WechatController {

	protected $api_host;
	protected $groups;
	protected $landusers;
	protected $pushers;
	protected $typeinfo;
	protected $manageinfo;
	protected $development;
	public function _initialize() {
		parent::_initialize();
		header('Access-Control-Allow-Origin: *');
		// $this->wechat_initialize();
		$this->groups = M('groups');
		$this->landusers = M('landuser');
		$this->pushers = M('pusher');
		$this->typeinfo = M('typeinfo');
		$this->manageinfo = M('manageinfo');
		$this->development = M('development');
		// $this->api_host = 'http://121.43.50.178:81/';
	}	
	public function index()
	{
		$openid = $this->getopenid();
		$tagsuser = $this->get_tags(104);
		$tagsland = $this->get_tags(103);
		$alltags = $this->testget_tags();
		$data = [
			"openid" => $openid,
			"系统管理员" => $tagsuser,
			"地推成员" => $tagsland,
			"alltags" => $alltags,
		];
		dump($data);
	}
	/**
	 * 接收code换取openid
	 * @return [type] [description]
	 */
	public function loginCode()
	{
		$info = $this->registerApi();
		if ((!empty($info['code']) || !empty($info['openid'])) && !empty($info['action_type'])) {
			if (!empty($info['openid'])) {
				$openid = $info['openid'];
			}else{
				$code = $info['code'];
				$openidres = $this->htmlopenid($code);
				$openid = $openidres['openid'];
			}
			if ($info['action_type'] == 1) {
				//id 104
				$tags = $this->get_tags(104);
				if (in_array($openid, $tags['data']['openid'])) {
					$code = 0;
					$message='系统管理员';
					$data['pusherInfo']['openid'] = $openid;
				}else{
					$code = 1111;
					$message='没有系统管理员权限';
					$data['pusherInfo']['openid'] = $openid;
				}
			}
			if ($info['action_type'] == 2) {
				//id 103
				$tags = $this->get_tags(103);
				$taguser = $this->get_tags(104);
				if (in_array($openid, $tags['data']['openid']) || in_array($openid, $taguser['data']['openid'])) {
					$map['openid'] = $openid;
					$temp = $this->pushers->where($map)->find();
					if (empty($temp)) {
						$code = 1111;
						$message='地推有权限,但是没有绑定手机号';
						$data['pusherInfo']['openid'] = $openid;
					}else{
						$code = 0;
						$message='地推有权限,手机号绑定过';
						$data['pusherInfo']['openid'] = $openid;
						$data['pusherInfo']['pusherId'] = $temp[0]['id'];
					}
				}else{
					$code = '-1111';
					$message='没有权限';
					$data['pusherInfo']['openid'] = $openid;
				}
			}
		}else{
			$code = '-3333';
			$message='传参错误';
			$data = [];
		}
		$this->databack($code,$message,$data);
	}
	public function offerManage()
	{
		$total = [
			"oPJay0w2TBokk1lU1REEN-dkYmFo", 
			"oPJay08IGitfoqIGnfM2QLpIL7rw", 
			"oPJay0x6NltflPYQs2riXqRdoiwM",
		];
		$openid = $this->getopenid();
		if (in_array($openid,$total)) {
			$offerurl = 'http://121.43.50.178:89/trade/offer/api?act=modifyGoodsOfferBySystem';
			// $offerurl = 'http://192.168.0.119:3333/trade/offer/api?act=modifyGoodsOfferBySystem';

			$data = [
				'action_type'=>'SOLD_OUT',
				'is_select_all'=>'1',
			];
			$info = $this->http_post($offerurl,json_encode($data));
			$info = json_decode($info,true);
			$msg = $info['message'];
		}else{
			$msg = '暂未开放';
		}
		exit($msg);
	}

	/**
	 * 权限查询接口  action_type (系统管理时传1,用户上报传2)  
	 * @return [type] [description]
	 */
	public function authorityJudge()
	{
		// $openid = $this->getopenid();
		// $tags = $this->get_tags(0);
		// dump($openid);
		// dump($tags);exit;
		$info = $this->registerApi();
		if (!empty($info['action_type'])) {
			$openid = $this->getopenid();
			$tags = $this->get_tags(0);
			if (in_array($openid, $tags['data']['openid'])) {
				$map['openid'] = $openid;
				$temp = $this->pushers->where($map)->find();
				if ($info['action_type'] == 1) {
					if (!empty($temp) && $temp[0]['isuser'] == 1) {
						$code = 0;
						$message='系统管理员';
						$data['pusherInfo']['openid'] = $openid;
					}else{
						$code = 1111;
						$message='没有权限';
						$data['pusherInfo']['openid'] = $openid;
					}
				}
				if ($info['action_type'] == 2) {
					if (empty($temp)) {
						$code = 1;
						$message='地推有权限,但是没有绑定手机号';
						$data['pusherInfo']['openid'] = $openid;
					}else{
						$code = 1;
						$message='地推有权限,手机号绑定过';
						$data['pusherInfo']['openid'] = $openid;
						$data['pusherInfo']['pusherId'] = $temp[0]['id'];
					}
				}
			}else{
				$code = -1111;
				$message='没有权限';
				$data = [];
			}
		}else{
			$code = -3333;
			$message='传参错误';
			$data = [];
		}
		
		$this->databack($code,$message,$data);
	}
	/**
	 * 添加分组
	 */
	public function addGroup()
	{	
		$info = $this->registerApi();
		if (isset($info['name']) && !empty($info['name'])) {
			$name = $info['name'];
			$map['name'] = $name;
			$temp = $this->groups->where($map)->find();
			if (empty($temp)) {
				$createtime = date('Y-m-d H:i:s');
				$data = [
					'name' => $name,
					'createtime' => $createtime,
					'updatetime' => $createtime,
				];
				$code = 0;
				$message='添加成功';
				$this->groups->add($data);
				$res = [];
			}else{
				$code = -1001;
				$message='组名已存在';
				$res = [];
			}
		}else{
			$code = -1002;
			$message='请输入组名';
			$res = [];
		}
		$this->databack($code,$message,$res);
	}
	/**
	 * 查询组
	 * @return [type] [description]
	 */
	public function allGroup()
	{
		//有成员的小组
		$temp = $this->groups->join('xz_pusher ON xz_groups.groupid = xz_pusher.groupid')
		->field('xz_groups.groupid,xz_groups.name,count(xz_pusher.groupid) as usercounts,xz_groups.createtime')
		->group('xz_groups.groupid')
		->select();
		//成员总数  在小组内的成员总数
		$userCount = $this->pushers->count();
		$map['groupid'] = array('neq',0);
		$inGroupUserCount = $this->pushers->where($map)->count();
		$groupuser = [];
		foreach ($temp as $key => $value) {
			$groupuser[$key] = $value['groupid'];
		}
		$allgroup = $this->groups->field('groupid,name,createtime')->order('createtime desc')->select();
		$groupCount = count($allgroup); 
		foreach ($allgroup as $key => $value) {
			if (in_array($value['groupid'],$groupuser)) {
				foreach ($temp as $k => $v) {
					if ($v['groupid'] == $value['groupid']) {
						$allgroup[$key]['usercounts'] = $v['usercounts'];
					}
				}
			}else{
				$allgroup[$key]['usercounts'] = 0;
			}
		}
		$count = [
			'userCount' => $userCount,//成员总数
			'inGroupUserCount' => $inGroupUserCount,//在小组内的成员总数
			'groupCount' => $groupCount,//小组数
		];
		$data['count'] = $count;
		$data['grouplist'] = $allgroup;
		$code = 0;
		$message = '请求成功';
		$this->databack($code,$message,$data);
	}
	/**
	 * 小组搜索
	 * @return [type] [description]
	 */
	public function groupSearch()
	{
		$info = $this->registerApi();
		if (isset($info['search']) && ($info['search'] || $info['search'] === "0")) {
			$search = $info['search'];
			$name['name'] = array('like',array('%'.$search.'%','%'.$search,$search.'%'),'OR');
			$map['xz_groups.name'] =array('like',array('%'.$search.'%','%'.$search,$search.'%'),'OR');
			$temp = $this->groups->join('xz_pusher ON xz_groups.groupid = xz_pusher.groupid')
			->field('xz_groups.groupid,xz_groups.name,count(xz_pusher.groupid) as usercounts,xz_groups.createtime')
			->group('xz_groups.groupid')
			->where($map)
			->select();
			$alltemp = $this->groups->field('groupid,name,createtime')->where($name)->order('createtime desc')->select();
			$groupCount = count($alltemp); 
			$groupuser = [];
			foreach ($temp as $key => $value) {
				$groupuser[$key] = $value['groupid'];
			}
			// $allgroup = $this->groups->field('groupid,name,createtime')->order('createtime desc')->select();
			foreach ($alltemp as $key => $value) {
				if (in_array($value['groupid'],$groupuser)) {
					foreach ($temp as $k => $v) {
						if ($v['groupid'] == $value['groupid']) {
							$alltemp[$key]['usercounts'] = $v['usercounts'];
						}
					}
				}else{
					$alltemp[$key]['usercounts'] = 0;
				}
			}
			foreach ($alltemp as $key => $value) {
				$usercounts += $value['usercounts'];
			}
			$count = [
				'userCount' => $usercounts,//成员总数
				'inGroupUserCount' => $usercounts,//在小组内的成员总数
				'groupCount' => $groupCount,//小组数
			];
			$data['count'] = $count;
			$data['grouplist'] = $alltemp;
			$code = 0;
			$message = '搜索成功';
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 小组详情
	 * @return [type] [description]
	 */
	public function groupDetails()
	{
		$info = $this->registerApi();
		if (isset($info['groupid']) && !empty($info['groupid'])) {
			$groupid = $info['groupid'];
			$map['groupid'] = $info['groupid'];
			$groupinfo = $this->groups
			->join('xz_pusher ON xz_groups.groupid = xz_pusher.groupid')
			->field('xz_groups.groupid,xz_groups.name as groupname,count(xz_pusher.groupid) as usercounts,xz_groups.createtime as groupcreatetime')
			->where('xz_groups.groupid='.$groupid)
			->select();
			$userinfo = $this->pushers
			->field('id,name as username,phone,createtime')
			->where('groupid='.$groupid)
			->select();
			$data['groupinfo'] = $groupinfo;
			$data['userinfo'] = $userinfo;
			$code = 0;
			$message = '请求成功';
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 小组成员搜索
	 * @return [type] [description]
	 */
	public function groupUserSearch()
	{
		$info = $this->registerApi();
		if (!empty($info['searchGroupId']) && ($info['searchName'] || $info['searchName'] === "0")) {
			$searchGroupId = $info['searchGroupId'];
			$search = $info['searchName'];
			$name['name'] = array('like',array('%'.$search.'%','%'.$search,$search.'%'),'OR');
			$name['phone'] = $search;
			$name['_logic'] = 'or';
			$map['_complex'] = $name;
			$map['groupid'] = $searchGroupId;
			$alltemp = $this->pushers->field('groupid,name as username,phone,createtime')->where($map)->order('createtime desc')->select();
			$data['searchInfo'] = $alltemp;
			$code = 0;
			$message = '搜索成功';
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 小组成员移除   //一个成员只能在一个小组   若选无分组传0
	 * @return [type] [description]
	 */
	public function groupUserRemove()
	{
		$info = $this->registerApi();
		if (isset($info['removeGroupId']) && (!empty($info['removeUserId']) && (!empty($info['addGroupId'] || $info['addGroupId'] === '0')))) {
			$removeGroupId = $info['removeGroupId'];
			$removeUserId = $info['removeUserId'];
			$addGroupId = $info['addGroupId'];
			if ($removeGroupId == $addGroupId) {
				$code = -1111;
				$message = '移动小组和原小组相同';
			}else{
				if (!empty($removeUserId)) {
					$name['groupid'] = $removeGroupId;
					$name['id'] = $removeUserId;
					$searchtemp = $this->pushers->where($name)->select();
					if (empty($searchtemp)) {
						$code = -1112;
						$message = '原组信息不存在';
					}else{
						$totalgroup['groupid'] = $addGroupId;
						$totalgroup['updatetime'] = date('Y-m-d H:i:s');
						$alltemp = $this->pushers->where($name)->save($totalgroup);
						$code = 0;
						$message = '移动成功';
					}
				}else{
					$code = -1113;
					$message = '参数为空';
				}
			}
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 小组成员添加  
	 * @return [type] [description]
	 */
	public function groupUserAdd()
	{
		$info = $this->registerApi();
		if ((!empty($info['currrentGroupId']) || $info['currrentGroupId'] === "0") && !empty($info['addUserId'])) {
			$currrentGroupId = $info['currrentGroupId'];//原小组
			$addUserId = $info['addUserId'];//成员id
			$name['id'] = $addUserId;
			$totalgroup['groupid'] = $currrentGroupId;
			$totalgroup['updatetime'] = date('Y-m-d H:i:s');
			$alltemp = $this->pushers->where($name)->save($totalgroup);
			if (empty($alltemp)) {
				$code = -2001;
				$message = '添加失败';
			}else{
				$code = 0;
				$message = '添加成功';
			}
		}else{
			$code = -1001;
			$message = '传参错误';
		}
		$this->databack($code,$message);
	}
	/**
	 * 除该小组外的所有成员
	 * @return [type] [description]
	 */
	public function selectUserName()
	{
		$info = $this->registerApi();
		if (!empty($info['groupId'])) {
			$name['groupid'] = array('neq',$info['groupId']);
			$searchtemp = $this->pushers->field('id,name,phone')->where($name)->select();
			$code = 0;
			$message = '查询成功';
			$data = $searchtemp;
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 修改名称
	 * @return [type] [description]
	 */
	public function groupNameEdit()
	{
		$info = $this->registerApi();
		if (!empty($info['groupId']) && !empty($info['groupName']) && !empty($info['togroupName'])) {
			if ($info['groupName'] == $info['togroupName']) {
				$code = -3001;
				$message = '新组名与原组名一致';
			}else{
				$map['name'] = $info['togroupName'];
				$res = $this->groups->where($map)->select();
				if (!empty($res)) {
					$code = -3002;
					$message = '组名已存在';
				}else{
					$name['groupid'] = $info['groupId'];
					$todata['name'] = $info['togroupName'];
					$todata['updatetime'] = date('Y-m-d H:i:s');
					$alltemp = $this->groups->where($name)->save($todata);
					if (empty($alltemp)) {
						$code = -2001;
						$message = '修改失败';
						$data = [];
					}else{
						$code = 0;
						$message = '修改成功';
						$data = $alltemp;
					}
				}
				
			}
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 删除小组
	 * @return [type] [description]
	 */
	public function deleteGroup()
	{
		$info = $this->registerApi();
		if (!empty($info['groupId'])) {
			$name['groupid'] = $info['groupId'];
			$searchtemp = $this->pushers->where($name)->select();
			if (empty($searchtemp)) {
				$this->groups->where($name)->delete();
				$code = 0;
				$message = '删除成功';
			}else{
				$code = -2001;
				$message = '不能删除';
			}
		}else{
			$code = -1001;
			$message = '传参错误';
		}
		$this->databack($code,$message);
	}
	/**
	 * 组选项
	 * @return [type] [description]
	 */
	public function allGroupOptions()
	{
		$options = $this->groups->field('groupid,name')->select();
		$nunname['groupid'] = '0';
		$nunname['name'] = '无分组';
		$options[] = $nunname;
		$code = 0;
		$message = '查询成功';
		$this->databack($code,$message,$options);
	}
	/**
	 * 成员添加
	 */
	public function addUser()
	{
		$info = $this->registerApi();
		if ((!empty($info['groupId']) || $info['groupId'] === "0") && !empty($info['userName'])) {
			$groupId = $info['groupId'];//原小组
			$userName = $info['userName'];//成员id
			$userPhone = $info['userPhone'];//成员id
			$mab['phone'] = $userPhone;
			$name['name'] = $userName;
			$totalgroup['createtime'] = date('Y-m-d H:i:s');
			$alltemp = $this->pushers->where($name)->select();
			if (!empty($alltemp)) {
				$code = -4001;
				$message = '成员名称已存在';
			}else{
				$phoneinfo = $this->pushers->where($mab)->select();
				if (!empty($phoneinfo)) {
					$code = -4002;
					$message = '成员手机号已存在';
				}else{
					$createtime = date('Y-m-d H:i:s');
					$adddata = [
						'name' => $userName,
						'phone' => $userPhone,
						'groupid' => $groupId,
						'createtime' => $createtime,
						'updatetime' => $createtime,
					];
					$this->pushers->add($adddata);
					$code = 0;
					$message='添加成功';
				}
			}
		}else{
			$code = -1001;
			$message = '传参错误';
		}
		$this->databack($code,$message);
	}
	/******************************成员列表***********************************/
	/**
	 * 成员列表
	 * @return [type] [description]
	 */
	public function userList()
	{
		//无小组成员
		$map['groupid'] = 0;
		$nogroup = $this->pushers->field('id,groupid,name as username,phone,status')->where($map)->select();
		foreach ($nogroup as $key => $value) {
			$nogroup[$key]['groupname'] = '暂无小组';
		}
		//有小组的成员
		$temp = $this->pushers->join('xz_groups ON xz_pusher.groupid = xz_groups.groupid')
		->field('xz_pusher.id,xz_groups.groupid,xz_groups.name as groupname,xz_pusher.name as username,xz_pusher.phone,xz_pusher.status')
		->group('xz_pusher.id')
		->order('xz_pusher.name')
		->select();
		$alltemp = array_merge($temp,$nogroup);
		$isgroup = count($temp);
		$alluser = count($alltemp);
		$allgroup = $options = $this->groups->field('groupid,name')->select();
		$nunname['groupid'] = '0';
		$nunname['name'] = '暂无分组';
		$allgroup[] = $nunname;
		$data['count'] = [
			"alluserCount" => $alluser,
			"groupuserCount" => $isgroup,
		];
		$data['group'] = $allgroup;
		$data['user'] = $alltemp;
		$code = 0;
		$message = '查询成功';
		$this->databack($code,$message,$data);
	}
	/**
	 * 跟据组查成员
	 * @return [type] [description]
	 */
	public function groupToUser()
	{
		$info = $this->registerApi();
		if (!empty($info['groupId']) || $info['groupId'] === "0") {
			if ($info['groupId'] == 0) {
				$map['groupid'] = $info['groupId'];
				$temp = $this->pushers
				->field('id,groupid,name as username,phone,status')
				->where($map)
				->order('xz_pusher.name')
				->select();
				foreach ($temp as $key => $value) {
					$temp[$key]['groupname'] = '暂无分组';
				}
			}else{
				$map['xz_pusher.groupid'] = $info['groupId'];
				$temp = $this->pushers->join('xz_groups ON xz_pusher.groupid = xz_groups.groupid')
				->field('xz_pusher.id,xz_groups.groupid,xz_groups.name as groupname,xz_pusher.name as username,xz_pusher.phone,xz_pusher.status')
				->group('xz_pusher.id')
				->where($map)
				->order('xz_pusher.name')
				->select();
			}
			$code = 0;
			$message = '查询成功';
			$data = $temp;
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 跟据手机号或者姓名查成员 传 searchName或searchPhone
	 * @return [type] [description]
	 */
	public function searchUser()
	{
		$info = $this->registerApi();
		if ($info['searchName'] || $info['searchName'] === "0") {
			$search = $info['searchName'];
			$name['name'] = $search;
			$name['phone'] = $search;
			$name['_logic'] = 'or';
			$no['_complex'] = $name;
			$groupinfo = $this->pushers->field('id,groupid,name as username,phone,status')->where($no)->select();
			if (empty($groupinfo)) {
				$data = [];
			}else{
				if (isset($groupinfo[0]['groupid']) && $groupinfo[0]['groupid'] == 0) {
					$groupinfo[0]['groupname'] = '暂无分组';
					$data = $groupinfo;
				}else{
					$cse['xz_pusher.name'] = $search;
					$cse['xz_pusher.phone'] = $search;
					$cse['_logic'] = 'or';
					$mo['_complex'] = $cse;
					$temp = $this->pushers->join('xz_groups ON xz_pusher.groupid = xz_groups.groupid')
					->field('xz_pusher.id,xz_groups.groupid,xz_groups.name as groupname,xz_pusher.name as username,xz_pusher.phone,xz_pusher.status')
					->group('xz_pusher.id')
					->where($mo)
					->order('xz_pusher.name')
					->select();
					$data = $temp;
				}
			}
			$code = 0;
			$message = '搜索成功';
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 地拖成员删除 传参 pusherStrId 为数组  删除成功会返回 noDeleteInfo 不能删除的人员信息，以及已经删除的 isDeleteInfo 人员信息，如果没有可以删除的，则isDeleteInfo为空
	 * @return [type] [description]
	 */
	public function pusherDelete()
	{
		$info = $this->registerArrApi();
		// $data = $info;
		if (!empty($info['pusherStrId'])) {
			$pusherStrId = $info['pusherStrId']; 
			$belongid = $this->landusers->field('belongid,viewuser')->select();
			foreach ($belongid as $key => $value) {
				$belong[$key] = $value['belongid'];
				$viewuser[$key] = json_decode($value['viewuser'],true);
			}
			foreach ($viewuser as $key => $value) {
				foreach ($value as $k => $v) {
					if (!in_array($v,$belong)) {
						$belong[] = $v;
					}
					
				}
			}
			foreach ($pusherStrId as $key => $value) {
				if (in_array($value,$belong)) {
					$nodelet[] = $value;
				}else{
					$isdelet[] = $value;
				}
			}
			if (empty($nodelet)) {
				$isDeleteInfo = [];
			}else{
				$map['id'] = array('in', $nodelet);
				$noDeleteInfo = $this->pushers->field('id,groupid,name,phone,status')->where($map)->select();
			}
			if (empty($isdelet)) {
				$isDeleteInfo = [];
			}else{
				$deletemap['id'] = array('in', $isdelet);
				$isDeleteInfo = $this->pushers->where($deletemap)->select();
				$deleteStatus = $this->pushers->where($deletemap)->delete();
			}
			$data['noDeleteInfo'] = $noDeleteInfo;
			$data['isDeleteInfo'] = $isDeleteInfo;
			$code = 0;
			$message = '删除成功';
			
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];		
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 改变成员状态    传参 pusherStrId 为数组  status 0为启用  1为禁用
	 * @return [type] [description]
	 */
	public function editStatus()
	{
		$info = $this->registerArrApi();
		if (isset($info['pusherStrId']) && isset($info['status'])) {
			$pusherStrId = $info['pusherStrId']; 
			$status = $info['status'];
			$createtime = date('Y-m-d H:i:s');
			$map['id'] = array('in', $pusherStrId);
			$saveinfo = [
				"status" => $status,
				"updatetime" => $createtime,
			];
			$data = $this->pushers->where($map)->save($saveinfo);
			$code = 0;
			$message = '更改成功';
			
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];		
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 成员分组 参数: groupId pusherStrId(数组)    返回值successCount 成功数  failCount失败数
	 * @return [type] [description]
	 */
	public function userGroup()
	{
		$info = $this->registerArrApi();
		if (!empty($info['pusherStrId']) && (!empty($info['groupId']) || $info['groupId'] === "0")) {
			$pusherStrId = $info['pusherStrId']; 
			$groupId = $info['groupId'];
			$createtime = date('Y-m-d H:i:s');
			$map['id'] = array('in', $pusherStrId);
			$map['status'] = 0;
			$countall = count($pusherStrId);
			$saveinfo = [
				"groupid" => $groupId,
				"updatetime" => $createtime,
			];
			$successCount = $this->pushers->where($map)->save($saveinfo);
			$data['successCount'] = $successCount;
			$failCount = $countall - $successCount;
			$data['failCount'] = $failCount;
			$code = 0;
			$message = '分组成功';
			
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];		
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 修改地推者信息 pusherId 地推人员id pusherName 地推人员要修改成的姓名 pusherPhone 地推人员要修改成的手机号
	 * @return [type] [description]
	 */
	public function editPusher()
	{
		$info = $this->registerApi();
		if (!empty($info['pusherId']) && (!empty($info['pusherName']) && !empty($info['pusherPhone']))) {
			$pusherId = $info['pusherId'];
			$pusherName = $info['pusherName'];
			$pusherPhone = $info['pusherPhone'];
			$createtime = date('Y-m-d H:i:s');
			$map['id'] = $pusherId;
			$res = $this->pushers->where($map)->select();
			if ($res[0]['name'] == $pusherName && $res[0]['phone'] == $pusherPhone) {
				$code = -3001;
				$message = '没有改变名称和手机号';
			}else if ($res[0]['name'] != $pusherName && $res[0]['phone'] != $pusherPhone) {
				$name['name'] = $pusherName;
				$resname = $this->pushers->where($name)->select();
				if (!empty($resname)) {
					$code = -3002;
					$message = '名称已存在';
				}else{
					$phoneinfo['phone'] = $pusherPhone;
					$phoneres = $this->pushers->where($phoneinfo)->select();
					if (!empty($phoneres)) {
						$code = -3003;
						$message = '手机号已存在';
					}else{
						$saveinfo = [
							'name' => $pusherName,
							'phone' => $pusherPhone,
							'updatetime' => $createtime,
						];
						$code = 0;
						$message = "手机号和名称更新成功";
						$data = $this->pushers->where($map)->save($saveinfo);
					}
				}
			}else if ($res[0]['name'] != $pusherName) {
				$name['name'] = $pusherName;
				$resname = $this->pushers->where($name)->select();
				if (!empty($resname)) {
					$code = -3002;
					$message = '名称已存在';
				}else{
					$saveinfo = [
						'name' => $pusherName,
						'updatetime' => $createtime,
					];
					$data = $this->pushers->where($map)->save($saveinfo);
					$code = 1;
					$message = "名称更新成功";
				}
				
			}else{
				$phoneinfo['phone'] = $pusherPhone;
				$phoneres = $this->pushers->where($phoneinfo)->select();
				if (!empty($phoneres)) {
					$code = -3003;
					$message = '手机号已存在';
				}else{
					$saveinfo = [
						'phone' => $pusherPhone,
						'updatetime' => $createtime,
					];
					$data = $this->pushers->where($map)->save($saveinfo);
					$code = 2;
					$message = "手机号更新成功";
				}
			}
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];		
		}
		$this->databack($code,$message,$data);
	}
	/**************************************数据统计***********************************************/
	/**
	 * 系统管理中的数据统计  今日和七天选参数 days   自定义时传 endTime  和  days (可选)若有传，若没有可不传，若传传时间戳 秒为单位:1526951517       定义actionType 1 为全部  2为搜索 actionType为2时传userName    比如一天:从今天的00:00 到后一天的00:00    两点:1.时间内添加的地推人员 2.时间内上报的 
	 *  上传数为
	 * @return [type] [description]
	 */
	public function systemData()
	{
		$info = $this->registerApi();
		if (!empty($info['actionType'])) {
			if (empty($info['endTime'])) {
				$endSec = time();
			}else{
				$endSec = $info['endTime'];

			}
			if (empty($info['days'])) {
				$days = 1;
			}else{
				$days = $info['days'];
			}
			$startSec = $endSec - (($days-1)*24*3600);
			$endTime = date('Y-m-d 23:59:59',$endSec);
			$startTime = date('Y-m-d 00:00:00',$startSec);
			//上报总数(加时间条件)
			$allmap['createtime'] = array('between',array($startTime,$endTime));
			$allCount = $this->landusers->field('count(userid) as allCount')->where($allmap)->select();
			$share['groupid'] = array('NEQ',0);
			$share['createtime'] = array('between',array($startTime,$endTime));
			$shareCount = $this->landusers->field('count(userid) as shareCount')->where($share)->select();
			$data['count']['allCount'] = $allCount[0]['allcount'];
			$data['count']['shareCount'] = $shareCount[0]['sharecount'];
			$data['count']['days'] = $days;
			$groupname = $this->groups->field('groupid,name')->select();
			foreach ($groupname as $key => $value) {
				$grouparr[$value['groupid']] = $value['name'];
			}
			if ($info['actionType'] == "1") {
				//时间内添加的地推人员
				// $map['xz_pusher.createtime'] = array('ELT',$endTime);
				$map['xz_landuser.createtime'] = array('between',array($startTime,$endTime));
				$map['xz_landuser.groupid'] = array('NEQ',0);
				$sharetemp = $this->pushers->join('xz_landuser ON xz_pusher.id = xz_landuser.belongid')
				->join('xz_groups ON xz_pusher.groupid = xz_groups.groupid')
				->field('xz_pusher.id as userId,xz_pusher.name as userName,xz_groups.name as groupName,count(xz_landuser.belongid) as shareCount')
				->group('xz_pusher.id')
				->where($map)
				->order('xz_pusher.name')
				->select();
				// $no['xz_landuser.groupid'] = 0;
				$no['xz_landuser.createtime'] = array('between',array($startTime,$endTime));
				$alltemp = $this->pushers
				->join('xz_landuser ON xz_pusher.id = xz_landuser.belongid')
				->field('xz_pusher.id as userId,xz_pusher.name as userName,count(xz_landuser.belongid) as userallCount,xz_landuser.groupid')
				->group('xz_pusher.id')
				->where($no)
				->select();
				foreach ($sharetemp as $key => $value) {
					$userid[] = $value['userid'];
					$select[$value['userid']] = $value['sharecount'];
				}
				foreach ($alltemp as $key => $value) {
					if (in_array($value['userid'],$userid)) {
						$alltemp[$key]['shareCount'] = $select[$value['userid']];
					}else{
						$alltemp[$key]['shareCount'] = 0;
					}
					if ($value['groupid'] == 0) {
						$alltemp[$key]['groupname'] = "暂无小组";
					}else{
						$alltemp[$key]['groupname'] = $grouparr[$value['groupid']];
					}
				}
				$data['count']['aboutUserCount'] = count($alltemp);
				$data['userList'] = $alltemp;
				$code = 0;
				$message = "查询成功";
			}else{
				$searchname = $info['username'];
				// $no['xz_landuser.groupid'] = 0;
				$no['xz_landuser.createtime'] = array('between',array($startTime,$endTime));
				$no['xz_pusher.name'] = $searchname;
				$alltemp = $this->pushers
				->join('xz_landuser ON xz_pusher.id = xz_landuser.belongid')
				->field('xz_pusher.id as userId,xz_pusher.name as userName,count(xz_landuser.belongid) as userallCount,xz_landuser.groupid')
				->group('xz_pusher.id')
				->where($no)
				->select();
				if (empty($alltemp)) {
					$data['userList'] = [];
				}else{
					$map['xz_landuser.createtime'] = array('between',array($startTime,$endTime));
					$map['xz_landuser.groupid'] = array('NEQ',0);
					$map['xz_pusher.name'] = $searchname;
					$sharetemp = $this->pushers->join('xz_landuser ON xz_pusher.id = xz_landuser.belongid')
					->join('xz_groups ON xz_pusher.groupid = xz_groups.groupid')
					->field('xz_pusher.id as userId,xz_pusher.name as userName,xz_groups.name as groupName,count(xz_landuser.belongid) as shareCount')
					->group('xz_pusher.id')
					->where($map)
					->select();
					if (empty($sharetemp)) {
						$alltemp[0]['shareCount'] = 0;
						$alltemp['groupname'] = "暂无小组";
					}else{
						$alltemp[0]['shareCount'] = $sharetemp[0]['sharecount'];
						$alltemp['groupname'] = $grouparr[$sharetemp[0]['groupid']];
					}
					$data['userList'] = $alltemp;
				}
				$data['count']['aboutUserCount'] = count($alltemp);
				$code = 0;
				$message = "查询成功";
			}
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];	
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 上报详情 pusherId  days  endTime  days(天数, 今天days为1,7天为7,自定义时如从5.17到5.22 days为6)  
			  endTime(只在自定义时间时传,截至时以s为单位的时间戳如:1526865117)    actionType  1 时表示全部  需要传
	 * @return [type] [description]
	 */
	public function reportDetails()
	{
		$info = $this->registerApi();
		if (!empty($info['actionType']) && !empty($info['pusherId'])) {
			$pusherId = $info['pusherId'];
			if (empty($info['endTime'])) {
				$endSec = time();
			}else{
				$endSec = $info['endTime'];
			}
			if (empty($info['days'])) {
				$days = 1;
			}else{
				$days = $info['days'];
			}
			$startSec = $endSec - (($days-1)*24*3600);
			$endTime = date('Y-m-d 23:59:59',$endSec);
			$startTime = date('Y-m-d 00:00:00',$startSec);
			$typeinfo = $this->typeinfo->select();
			foreach ($typeinfo as $key => $value) {
				$typearr[$value['id']] = $value['name'];
			}
			$manageinfo = $this->manageinfo->select();
			foreach ($manageinfo as $key => $value) {
				$managearr[$value['id']] = $value['name'];
			}
			$developmentinfo = $this->development->select();
			foreach ($developmentinfo as $key => $value) {
				$developmentarr[$value['id']] = $value['name'];
			}
			$groupname = $this->groups->field('groupid,name')->select();
			foreach ($groupname as $key => $value) {
				$grouparr[$value['groupid']] = $value['name'];
			}
			$sexarr = [
				'0'=>'男',
				'1'=>'女'
			];
			if ($info['actionType'] == 1) {
				$no['xz_landuser.createtime'] = array('between',array($startTime,$endTime));
				$no['xz_landuser.belongid'] = $pusherId;
				$temp = $this->landusers->join('xz_pusher ON xz_landuser.belongid = xz_pusher.id')
				->field('xz_pusher.id as userid,xz_pusher.name as pushersname,xz_landuser.groupid,xz_landuser.username as landusername,xz_landuser.sex,xz_landuser.typeid,xz_landuser.phone,xz_landuser.province,xz_landuser.city,xz_landuser.area,xz_landuser.address,xz_landuser.manageid,xz_landuser.development,xz_landuser.content,xz_landuser.createtime')
				->where($no)
				->order('xz_landuser.createtime desc')
				->select();
				$data = [];
				if (!empty($temp)) {
					foreach ($temp as $key => $value) {
						$temp[$key]['typename'] = $typearr[$value['typeid']];
						$temp[$key]['sexname'] = $sexarr[$value['sex']];
						foreach (json_decode($value['manageid'],true) as $k => $v) {
							// $temp[$key]['managename'][$v] = $managearr[$v];
							$temp[$key]['manageInfo'][$k]['manageid'] = $v;
							$temp[$key]['manageInfo'][$k]['managename'] = $managearr[$v];
						}
						foreach (json_decode($value['development'],true) as $k => $v) {
							// $temp[$key]['developmentname'][$v] = $developmentarr[$v];
							$temp[$key]['developmentInfo'][$k]['developmentid'] = $v;
							$temp[$key]['developmentInfo'][$k]['developmentname'] = $developmentarr[$v];
						}
						if ($value['groupid'] == 0) {
							$temp[$key]['groupname'] = "暂无小组";
						}else{
							$temp[$key]['groupname'] = $grouparr[$value['groupid']];
						}
						$data[$key]['pusherid'] = $temp[$key]['userid'];
						$data[$key]['pushername'] = $temp[$key]['pushersname'];
						$data[$key]['groupid'] = $temp[$key]['groupid'];
						$data[$key]['groupname'] = $temp[$key]['groupname'];
						$data[$key]['landusername'] = $temp[$key]['landusername'];
						$data[$key]['sexid'] = $temp[$key]['sex'];
						$data[$key]['sexname'] = $temp[$key]['sexname'];
						$data[$key]['typeid'] = $temp[$key]['typeid'];
						$data[$key]['typename'] = $temp[$key]['typename'];
						$data[$key]['landuserphone'] = $temp[$key]['phone'];
						$data[$key]['province'] = $temp[$key]['province'];
						$data[$key]['city'] = $temp[$key]['city'];
						$data[$key]['area'] = $temp[$key]['area'];
						$data[$key]['address'] = $temp[$key]['address'];
						$data[$key]['content'] = json_decode($temp[$key]['content'],true);
						$data[$key]['createtime'] = $temp[$key]['createtime'];
						$data[$key]['manageInfo'] = $temp[$key]['manageInfo'];
						$data[$key]['developmentInfo'] = $temp[$key]['developmentInfo'];
						
					}
				}else{
					$data = [];
				}
				$code = 0;
				$message = '成功';
			}else{
				if (isset($info['searchName'])) {
					$name['xz_landuser.username'] = $info['searchName'];
					$name['xz_landuser.phone'] = $info['searchName'];
					$name['_logic'] = 'or';
					$no['_complex'] = $name;
					$no['xz_landuser.createtime'] = array('between',array($startTime,$endTime));
					$no['xz_landuser.belongid'] = $pusherId;
					$temp = $this->landusers->join('xz_pusher ON xz_landuser.belongid = xz_pusher.id')
					->field('xz_pusher.id as userid,xz_pusher.name as pushersname,xz_landuser.groupid,xz_landuser.username as landusername,xz_landuser.sex,xz_landuser.typeid,xz_landuser.phone,xz_landuser.province,xz_landuser.city,xz_landuser.area,xz_landuser.address,xz_landuser.manageid,xz_landuser.development,xz_landuser.content,xz_landuser.createtime')
					->where($no)
					->select();
					if (!empty($temp)) {
						foreach ($temp as $key => $value) {
							$temp[$key]['typename'] = $typearr[$value['typeid']];
							$temp[$key]['sexname'] = $sexarr[$value['sex']];
							foreach (json_decode($value['manageid'],true) as $k => $v) {
								// $temp[$key]['managename'][$v] = $managearr[$v];
								$temp[$key]['manageInfo'][$k]['manageid'] = $v;
								$temp[$key]['manageInfo'][$k]['managename'] = $managearr[$v];
							}
							foreach (json_decode($value['development'],true) as $k => $v) {
								// $temp[$key]['developmentname'][$v] = $developmentarr[$v];
								$temp[$key]['developmentInfo'][$k]['developmentid'] = $v;
								$temp[$key]['developmentInfo'][$k]['developmentname'] = $developmentarr[$v];
							}
							if ($value['groupid'] == 0) {
								$temp[$key]['groupname'] = "暂无小组";
							}else{
								$temp[$key]['groupname'] = $grouparr[$value['groupid']];
							}
							$data[$key]['pusherid'] = $temp[$key]['userid'];
							$data[$key]['pushername'] = $temp[$key]['pushersname'];
							$data[$key]['groupid'] = $temp[$key]['groupid'];
							$data[$key]['groupname'] = $temp[$key]['groupname'];
							$data[$key]['landusername'] = $temp[$key]['landusername'];
							$data[$key]['sexid'] = $temp[$key]['sex'];
							$data[$key]['sexname'] = $temp[$key]['sexname'];
							$data[$key]['typeid'] = $temp[$key]['typeid'];
							$data[$key]['typename'] = $temp[$key]['typename'];
							$data[$key]['landuserphone'] = $temp[$key]['phone'];
							$data[$key]['province'] = $temp[$key]['province'];
							$data[$key]['city'] = $temp[$key]['city'];
							$data[$key]['area'] = $temp[$key]['area'];
							$data[$key]['address'] = $temp[$key]['address'];
							$data[$key]['content'] = json_decode($temp[$key]['content'],true);
							$data[$key]['createtime'] = $temp[$key]['createtime'];
							$data[$key]['manageInfo'] = $temp[$key]['manageInfo'];
							$data[$key]['developmentInfo'] = $temp[$key]['developmentInfo'];
						}
					}else{
						$data = [];
					}
					$code = 0;
					$message = '成功';
				}else{
					$code = -2001;
					$message = '请输入要搜索的名字';
					$data = [];	
				}
			}
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];	
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 经营品种和拓展成果所有类
	 * @return [type] [description]
	 */
	public function allManageDevelopment()
	{
		$data['manage'] = $this->manageinfo->select();
		$data['development'] = $this->development->select();
		$data['type'] = $this->typeinfo->select();
		$code = 0;
		$message = '成功';
		$this->databack($code,$message,$data);
	}
/**************************************************用户上报****************************************************************/
	public function phoneBind()
	{
		$info = $this->registerApi();
		if (!empty($info['openid']) && !empty($info['phone'])) {
			$name['openid'] = $info['openid'];
			$openidtemp = $this->pushers->where($name)->select();
			if (!empty($openidtemp)) {
				$code = -2001;
				$message = '该微信号已绑定';
				$data = [];
			}else{
				$map['phone'] = $info['phone'];
				$temp = $this->pushers->where($map)->select();
				if (empty($temp)) {
					$code = -2002;
					$message = '没有该手机号';
					$data = [];
				}else{
					if (!empty($temp[0]['openid']) && $temp[0]['openid'] != $info['openid']) {
						$code = -2003;
						$message = '该手机号已绑定';
						$data = [];
					}else{
						$createtime = date('Y-m-d H:i:s');
						$saveinfo = [
							'openid' => $info['openid'],
							'updatetime' => $createtime,
						];
						$res = $this->pushers->where($map)->save($saveinfo);
						$data['pusherInfo']['pusherId'] = $temp[0]['id'];
						$code = 0;
						$message = '手机号绑定成功';
					}
				}
			}
		}else{
			$code = -1001;
			$message = '参数错误';
			$data = [];
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 新增上报 pusherId 进来时接口给的 pusherId
	 * @return [type] [description]
	 */
	public function addReport()
	{
		$info = $this->registerArrApi();
		if (!empty($info['name']) && (!empty($info['phone']) && !empty($info['pusherId']))) {
			$pusherId = $info['pusherId'];
			$name = $info['name'];
			$phone = $info['phone'];
			$phonemap['phone'] = $phone;
			$res = $this->landusers->where($phonemap)->select();
			if (empty($res)) {
				$sex = $info['sexid'];
				$typeid = $info['typeid'];
				$manageid = $info['manageid'];
				$development = $info['developmentid'];
				$province = $info['province'];
				$city = $info['city'];
				$area = $info['area'];
				$address = $info['address'];
				$groupmap['id'] = $pusherId;
				$temp = $this->pushers->where($groupmap)->select();
				$groupid = $temp[0]['groupid'];
				$belongid = $pusherId;
				if ($groupid == 0) {
					$viewuser = 0;
				}else{
					$groupnom['groupid'] = $groupid;
					$resgroup = $this->pushers->field('id')->where($groupnom)->select();
					foreach ($resgroup as $key => $value) {
						$view[] = $value['id'];
					}
					$viewuser = $view;
				}
				$createtime = date('Y-m-d H:i:s');
				$addinfo = [
					'username' => $name,
					'phone' => $phone,
					'sex' => $sex,
					'typeid' => $typeid,
					'manageid' => json_encode($manageid),
					'development' => json_encode($development),
					'province' => $province,
					'city' => $city,
					'area' => $area,
					'address' => $address,
					'createtime' => $createtime,
					'updatetime' => $createtime,
					'belongid' => $belongid,
					'viewuser' => json_encode($viewuser),
					'groupid' => $groupid,
					'content' => '',
				];
				$this->landusers->add($addinfo);
				$code = 0;
				$message = '上报成功';
			}else{
				$code = -3001;
				$message = '手机号已存在';
			}
		}else{
			$code = -1001;
			$message = '参数错误';
		}
		$this->databack($code,$message);
	}
	/**
	 * 用户上报统计
	 * @return [type] [description]
	 */
	public function landuserReport()
	{
		$info = $this->registerApi();
		if (!empty($info['actionType']) && !empty($info['pusherId'])) {
			if (empty($info['endTime'])) {
				$endSec = time();
			}else{
				$endSec = $info['endTime'];

			}
			if (empty($info['days'])) {
				$days = 1;
			}else{
				$days = $info['days'];
			}
			$startSec = $endSec - (($days-1)*24*3600);
			$endTime = date('Y-m-d 23:59:59',$endSec);
			$startTime = date('Y-m-d 00:00:00',$startSec);
			//上报总数(加时间条件)
			
			$aboutno['xz_landuser.createtime'] = array('between',array($startTime,$endTime));
			$temp = $this->landusers->join('xz_pusher ON xz_landuser.belongid = xz_pusher.id')
			->field('xz_landuser.userid as landuserid,xz_pusher.id as pusherid,xz_pusher.name as pushersname,xz_landuser.groupid,xz_landuser.username as landusername,xz_landuser.sex,xz_landuser.typeid,xz_landuser.phone,xz_landuser.province,xz_landuser.city,xz_landuser.area,xz_landuser.address,xz_landuser.manageid,xz_landuser.development,xz_landuser.content,xz_landuser.createtime,xz_landuser.viewuser')
			->where($aboutno)
			->order('xz_landuser.createtime desc')
			->select();
			$allAbout = [];
			$allgroup = [];
			foreach ($temp as $key => $value) {
				if (in_array($info['pusherId'],json_decode($value['viewuser'],true)) || ($temp[$key]['groupid'] == 0 && $temp[$key]['pusherid'] == $info['pusherId'])) {
					$allAbout[] = $temp[$key];
				}
				if (in_array($info['pusherId'],json_decode($value['viewuser'],true))) {
					$allShareAbout[] = $temp[$key];
					$ingroup = json_decode($value['viewuser'],true);
					foreach ($ingroup as $k => $v) {
						if (!in_array($v,$allgroup)) {
							$allgroup[] = $v;
						}
					}
				}
			}
			$res = [];
			foreach ($allAbout as $key => $value) {
				$res[$value['createtime']][$value['landuserid']] = $value;
				$datearr[] = $value['createtime'];
			}
			for ($i=0; $i < $days; $i++) { 
				$date = date("Y-m-d",strtotime("+".$i."day",$startSec));
				if (!in_array($date,$datearr)) {
					$countInfo[$i]['createtime'] = $date;
					$countInfo[$i]['counts'] = 0;
				}else{
					$countInfo[$i]['createtime'] = $date;
					$countInfo[$i]['counts'] = count($res[$date]);
				}
			}
			$nono['belongid'] = $info['pusherId'];
			$nono['groupid'] = 0;
			$nogroup = $this->landusers->where($nono)->select();
			if (empty($nogroup)) {
				$othershe = 0;
			}else{
				$othershe = 1;
			}
			$allaboutUser = count($allgroup);
			$data['count']['allCount'] = count($allAbout);
			$data['count']['shareCount'] = count($allShareAbout) + $othershe;
			$data['count']['aboutUserCount'] = $allaboutUser;
			$data['count']['days'] = $days;
			$data['daysList'] = $countInfo;
			$code = 0;
			$message = "查询成功";
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];	
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 用户搜索 actionType 1 全范围搜索 2 时间和名字搜索(传传要搜索日期当天的时间戳和名字)  3 时间搜索(传要搜索日期当天的时间戳)
	 * @return [type] [description]
	 */
	public function landuserSearch()
	{
		$info = $this->registerApi();
		if (!empty($info['actionType']) && !empty($info['pusherId'])) {
			if (empty($info['endTime'])) {
				$endSec = time();
			}else{
				$endSec = $info['endTime'];
			}
			if (empty($info['days'])) {
				$days = 1;
			}else{
				$days = $info['days'];
			}
			$startSec = $endSec - (($days-1)*24*3600);
			$endTime = date('Y-m-d 23:59:59',$endSec);
			$startTime = date('Y-m-d 00:00:00',$startSec);
			$typeinfo = $this->typeinfo->select();
			foreach ($typeinfo as $key => $value) {
				$typearr[$value['id']] = $value['name'];
			}
			$manageinfo = $this->manageinfo->select();
			foreach ($manageinfo as $key => $value) {
				$managearr[$value['id']] = $value['name'];
			}
			$developmentinfo = $this->development->select();
			foreach ($developmentinfo as $key => $value) {
				$developmentarr[$value['id']] = $value['name'];
			}
			$groupname = $this->groups->field('groupid,name')->select();
			foreach ($groupname as $key => $value) {
				$grouparr[$value['groupid']] = $value['name'];
			}
			$sexarr = [
				'0'=>'男',
				'1'=>'女'
			];
			if ($info['actionType'] == 1) {
				if (!isset($info['search'])) {
					$code = -2001;
					$message = '请输入要搜索的手机号';
					$data = [];
				}else{
					$search = $info['search'];
					$allno['xz_landuser.phone'] = $search;
					$temp = $this->landusers->join('xz_pusher ON xz_landuser.belongid = xz_pusher.id')
					->field('xz_pusher.id as pusherid,xz_pusher.name as pushersname,xz_landuser.userid as landuserid,xz_landuser.groupid,xz_landuser.username as landusername,xz_landuser.sex,xz_landuser.typeid,xz_landuser.phone,xz_landuser.province,xz_landuser.city,xz_landuser.area,xz_landuser.address,xz_landuser.manageid,xz_landuser.development,xz_landuser.content,xz_landuser.createtime,xz_landuser.viewuser')
					->where($allno)
					->order('xz_landuser.createtime desc')
					->select();
					if (!empty($temp)) {
						foreach ($temp as $key => $value) {
							if (in_array($info['pusherId'],json_decode($value['viewuser'],true)) || ($temp[$key]['groupid'] == 0 && $temp[$key]['groupid'] == $info['pusherId'])) {
								$temp[$key]['typename'] = $typearr[$value['typeid']];
								$temp[$key]['sexname'] = $sexarr[$value['sex']];
								foreach (json_decode($value['manageid'],true) as $k => $v) {
									// $temp[$key]['managename'][$v] = $managearr[$v];
									$temp[$key]['manageInfo'][$k]['manageid'] = $v;
									$temp[$key]['manageInfo'][$k]['managename'] = $managearr[$v];
								}
								foreach (json_decode($value['development'],true) as $k => $v) {
									// $temp[$key]['developmentname'][$v] = $developmentarr[$v];
									$temp[$key]['developmentInfo'][$k]['developmentid'] = $v;
									$temp[$key]['developmentInfo'][$k]['developmentname'] = $developmentarr[$v];
								}
								if ($value['groupid'] == 0) {
									$temp[$key]['groupname'] = "暂无小组";
								}else{
									$temp[$key]['groupname'] = $grouparr[$value['groupid']];
								}
								$data[$key]['pusherid'] = $temp[$key]['pusherid'];
								$data[$key]['pushername'] = $temp[$key]['pushersname'];
								$data[$key]['groupid'] = $temp[$key]['groupid'];
								$data[$key]['groupname'] = $temp[$key]['groupname'];
								$data[$key]['landuserid'] = $temp[$key]['landuserid'];
								$data[$key]['landusername'] = $temp[$key]['landusername'];
								$data[$key]['sexid'] = $temp[$key]['sex'];
								$data[$key]['sexname'] = $temp[$key]['sexname'];
								$data[$key]['typeid'] = $temp[$key]['typeid'];
								$data[$key]['typename'] = $temp[$key]['typename'];
								$data[$key]['landuserphone'] = $temp[$key]['phone'];
								$data[$key]['province'] = $temp[$key]['province'];
								$data[$key]['city'] = $temp[$key]['city'];
								$data[$key]['area'] = $temp[$key]['area'];
								$data[$key]['address'] = $temp[$key]['address'];
								$data[$key]['content'] = json_decode($temp[$key]['content'],true);
								$data[$key]['createtime'] = $temp[$key]['createtime'];
								$data[$key]['manageInfo'] = $temp[$key]['manageInfo'];
								$data[$key]['developmentInfo'] = $temp[$key]['developmentInfo'];
							}
							
						}
					}else{
						$data = [];
					}
					$code = 0;
					$message = '成功';
				}
			}else if($info['actionType'] == 2){
				if (isset($info['search']) && isset($info['currentSec'])) {
					$search = $info['search'];
					$currentDate = date('Y-m-d',$info['currentSec']);
					$name['xz_landuser.username'] = array('like',array('%'.$search.'%','%'.$search,$search.'%'),'OR');
					$name['xz_landuser.phone'] = $search;
					$name['_logic'] = 'or';
					$allno['_complex'] = $name;
					$allno['xz_landuser.createtime'] = $currentDate;
					$temp = $this->landusers->join('xz_pusher ON xz_landuser.belongid = xz_pusher.id')
					->field('xz_pusher.id as userid,xz_pusher.name as pushersname,xz_landuser.groupid,xz_landuser.userid as landuserid,xz_landuser.username as landusername,xz_landuser.sex,xz_landuser.typeid,xz_landuser.phone,xz_landuser.province,xz_landuser.city,xz_landuser.area,xz_landuser.address,xz_landuser.manageid,xz_landuser.development,xz_landuser.content,xz_landuser.createtime,xz_landuser.viewuser')
					->where($allno)
					->select();
					$sql = $this->landusers->getLastSql();
					// $data['sql'] = $sql;
					if (!empty($temp)) {
						foreach ($temp as $key => $value) {
							if (in_array($info['pusherId'],json_decode($value['viewuser'],true)) || ($temp[$key]['groupid'] == 0 && $temp[$key]['groupid'] == $info['pusherId'])) {
								$temp[$key]['typename'] = $typearr[$value['typeid']];
								$temp[$key]['sexname'] = $sexarr[$value['sex']];
								foreach (json_decode($value['manageid'],true) as $k => $v) {
									// $temp[$key]['managename'][$v] = $managearr[$v];
									$temp[$key]['manageInfo'][$k]['manageid'] = $v;
									$temp[$key]['manageInfo'][$k]['managename'] = $managearr[$v];
								}
								foreach (json_decode($value['development'],true) as $k => $v) {
									// $temp[$key]['developmentname'][$v] = $developmentarr[$v];
									$temp[$key]['developmentInfo'][$k]['developmentid'] = $v;
									$temp[$key]['developmentInfo'][$k]['developmentname'] = $developmentarr[$v];
								}
								if ($value['groupid'] == 0) {
									$temp[$key]['groupname'] = "暂无小组";
								}else{
									$temp[$key]['groupname'] = $grouparr[$value['groupid']];
								}
								$data[$key]['pusherid'] = $temp[$key]['userid'];
								$data[$key]['pushername'] = $temp[$key]['pushersname'];
								$data[$key]['groupid'] = $temp[$key]['groupid'];
								$data[$key]['groupname'] = $temp[$key]['groupname'];
								$data[$key]['landuserid'] = $temp[$key]['landuserid'];
								$data[$key]['landusername'] = $temp[$key]['landusername'];
								$data[$key]['sexid'] = $temp[$key]['sex'];
								$data[$key]['sexname'] = $temp[$key]['sexname'];
								$data[$key]['typeid'] = $temp[$key]['typeid'];
								$data[$key]['typename'] = $temp[$key]['typename'];
								$data[$key]['landuserphone'] = $temp[$key]['phone'];
								$data[$key]['province'] = $temp[$key]['province'];
								$data[$key]['city'] = $temp[$key]['city'];
								$data[$key]['area'] = $temp[$key]['area'];
								$data[$key]['address'] = $temp[$key]['address'];
								$data[$key]['content'] = json_decode($temp[$key]['content'],true);
								$data[$key]['createtime'] = $temp[$key]['createtime'];
								$data[$key]['manageInfo'] = $temp[$key]['manageInfo'];
								$data[$key]['developmentInfo'] = $temp[$key]['developmentInfo'];
							}
						}
					}else{
						$data = [];
					}
					$code = 0;
					$message = '成功';
				}else{
					$code = -2001;
					$message = '传入要搜索的时间戳或名字';
					$data = [];
					
				}
					
			}else{
				if (isset($info['currentSec'])) {
					$currentDate = date('Y-m-d',$info['currentSec']);
					$allno['xz_landuser.createtime'] = $currentDate;
					$temp = $this->landusers->join('xz_pusher ON xz_landuser.belongid = xz_pusher.id')
					->field('xz_pusher.id as pusherid,xz_pusher.name as pushersname,xz_landuser.groupid,xz_landuser.userid as landuserid,xz_landuser.username as landusername,xz_landuser.sex,xz_landuser.typeid,xz_landuser.phone,xz_landuser.province,xz_landuser.city,xz_landuser.area,xz_landuser.address,xz_landuser.manageid,xz_landuser.development,xz_landuser.content,xz_landuser.createtime,xz_landuser.viewuser')
					->where($allno)
					->select();
					if (!empty($temp)) {
						
						foreach ($temp as $key => $value) {
							if (in_array($info['pusherId'],json_decode($value['viewuser'],true)) || ($temp[$key]['groupid'] == 0 && $temp[$key]['groupid'] == $info['pusherId'])) {
								$temp[$key]['typename'] = $typearr[$value['typeid']];
								$temp[$key]['sexname'] = $sexarr[$value['sex']];
								foreach (json_decode($value['manageid'],true) as $k => $v) {
									// $temp[$key]['managename'][$v] = $managearr[$v];
									$temp[$key]['manageInfo'][$k]['manageid'] = $v;
									$temp[$key]['manageInfo'][$k]['managename'] = $managearr[$v];
								}
								foreach (json_decode($value['development'],true) as $k => $v) {
									// $temp[$key]['developmentname'][$v] = $developmentarr[$v];
									$temp[$key]['developmentInfo'][$k]['developmentid'] = $v;
									$temp[$key]['developmentInfo'][$k]['developmentname'] = $developmentarr[$v];
								}
								if ($value['groupid'] == 0) {
									$temp[$key]['groupname'] = "暂无小组";
								}else{
									$temp[$key]['groupname'] = $grouparr[$value['groupid']];
								}
								$data[$key]['pusherid'] = $temp[$key]['pusherid'];
								$data[$key]['pushername'] = $temp[$key]['pushersname'];
								$data[$key]['groupid'] = $temp[$key]['groupid'];
								$data[$key]['groupname'] = $temp[$key]['groupname'];
								$data[$key]['landuserid'] = $temp[$key]['landuserid'];
								$data[$key]['landusername'] = $temp[$key]['landusername'];
								$data[$key]['sexid'] = $temp[$key]['sex'];
								$data[$key]['sexname'] = $temp[$key]['sexname'];
								$data[$key]['typeid'] = $temp[$key]['typeid'];
								$data[$key]['typename'] = $temp[$key]['typename'];
								$data[$key]['landuserphone'] = $temp[$key]['phone'];
								$data[$key]['province'] = $temp[$key]['province'];
								$data[$key]['city'] = $temp[$key]['city'];
								$data[$key]['area'] = $temp[$key]['area'];
								$data[$key]['address'] = $temp[$key]['address'];
								$data[$key]['content'] = json_decode($temp[$key]['content'],true);
								$data[$key]['createtime'] = $temp[$key]['createtime'];
								$data[$key]['manageInfo'] = $temp[$key]['manageInfo'];
								$data[$key]['developmentInfo'] = $temp[$key]['developmentInfo'];
							}
							
						}
					}else{
						$data = [];
					}
					$code = 0;
					$message = '成功';
				}else{
					$code = -2001;
					$message = '请输入要搜索的名字';
					$data = [];
				}
			}
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];	
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 修改上报信息
	 * @return [type] [description]
	 */
	public function editReport()
	{
		$info = $this->registerArrApi();
		if (!empty($info['name']) && (!empty($info['phone']) && !empty($info['landuserId']))) {
			$name = $info['name'];
			$phone = $info['phone'];
			$phonemap['phone'] = $phone;
			$phonemap['username'] = $name;
			$phonemap['userid'] = $info['landuserId'];
			$res = $this->landusers->where($phonemap)->select();
			if (!empty($res)) {
				$typeid = $info['typeid'];
				$manageid = $info['manageid'];
				$development = $info['developmentid'];
				$province = $info['province'];
				$city = $info['city'];
				$area = $info['area'];
				$address = $info['address'];
				$createtime = date('Y-m-d H:i:s');
				$saveinfo = [
					'typeid' => $typeid,
					'manageid' => json_encode($manageid),
					'development' => json_encode($development),
					'province' => $province,
					'city' => $city,
					'area' => $area,
					'address' => $address,
					'updatetime' => $createtime,
				];
				$saveno['userid'] = $info['landuserId'];
				$this->landusers->where($saveno)->save($saveinfo);
				$code = 0;
				$message = '修改成功';
			}else{
				$code = -3001;
				$message = '手机号、名字不能修改';
			}
		}else{
			$code = -1001;
			$message = '参数错误';
		}
		$this->databack($code,$message);
	}
	/**
	 * 删除上报信息
	 * @return [type] [description]
	 */
	public function deleteReport()
	{
		$info = $this->registerArrApi();
		if (!empty($info['landuserStrId'])) {
			$map['userid'] = array('in', $info['landuserStrId']);
			$data = $this->landusers->where($map)->delete();
			$code = 0;
			$message = '删除成功';
			
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];		
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 添加备注信息
	 */
	public function addContent()
	{
		$info = $this->registerArrApi();
		if (isset($info['content']) && (!empty($info['pusherId']) && !empty($info['landuserStrId']))) {
			$content = $info['content'];
			$userno['id'] = $info['pusherId'];
			$res = $this->pushers->where($userno)->select();
			$contentname = $res[0]['name'];
			$map['userid'] = array('in', $info['landuserStrId']);

			$oldcontetn = $this->landusers->where($map)->select();
			foreach ($oldcontetn as $key => $value) {
				$oldcon[$value['userid']] = json_decode($value['content'],true);
			}
			$createtime = date('Y-m-d H:i:s');
			$contentarr['contentby'] = $contentname;
			$contentarr['createtime'] = $createtime;
			$contentarr['content'] = $content;
			foreach ($info['landuserStrId'] as $key => $value) {
				if (empty($oldcon[$value])) {
					$contentall[$key][] = $contentarr;
				}else{
					$contentall[$key] = $oldcon[$value];
					$contentall[$key][] = $contentarr;
				}
				$savedata['content'] = json_encode($contentall[$key]);
				$savedata['createtime'] = $createtime;
				$namp['userid'] = $value;
				$data = $this->landusers->where($namp)->save($savedata);
			}
			$code = 0;
			$message='备注成功';
		}else{
			$data = [];
			$code = -1001;
			$message='参数错误';
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 成果统计 allCount          (上报分享数)
			  shareCount        (上报总数)
			  myshareCount      (我的分享数)
			  otherShareCount   (他人的分享数)
	 * @return [type] [description]
	 */
	public function achieveData()
	{
		$info = $this->registerApi();
		if (!empty($info['actionType']) && !empty($info['pusherId'])) {
			if (empty($info['endTime'])) {
				$endSec = time();
			}else{
				$endSec = $info['endTime'];

			}
			if (empty($info['days'])) {
				$days = 1;
			}else{
				$days = $info['days'];
			}
			$startSec = $endSec - (($days-1)*24*3600);
			$endTime = date('Y-m-d 23:59:59',$endSec);
			$startTime = date('Y-m-d 00:00:00',$startSec);
			//上报总数(加时间条件)
			
			$aboutno['xz_landuser.createtime'] = array('between',array($startTime,$endTime));
			$temp = $this->landusers->join('xz_pusher ON xz_landuser.belongid = xz_pusher.id')
			->field('xz_landuser.userid as landuserid,xz_pusher.id as pusherid,xz_pusher.name as pushersname,xz_landuser.groupid,xz_landuser.username as landusername,xz_landuser.sex,xz_landuser.typeid,xz_landuser.phone,xz_landuser.province,xz_landuser.city,xz_landuser.area,xz_landuser.address,xz_landuser.manageid,xz_landuser.development,xz_landuser.content,xz_landuser.createtime,xz_landuser.viewuser')
			->where($aboutno)
			->order('xz_landuser.createtime desc')
			->select();
			$allAbout = [];
			$allgroup = [];
			foreach ($temp as $key => $value) {
				if (in_array($info['pusherId'],json_decode($value['viewuser'],true)) || ($temp[$key]['groupid'] == 0 && $temp[$key]['pusherid'] == $info['pusherId'])) {
					$allAbout[] = $temp[$key];
				}
				if (in_array($info['pusherId'],json_decode($value['viewuser'],true))) {
					$allShareAbout[] = $temp[$key];
					$ingroup = json_decode($value['viewuser'],true);
					foreach ($ingroup as $k => $v) {
						if (!in_array($v,$allgroup)) {
							$allgroup[] = $v;
						}
					}
				}
			}
			$res = [];
			foreach ($allAbout as $key => $value) {
				$res[$value['createtime']][$value['landuserid']] = $value;
				$datearr[] = $value['createtime'];
			}
			foreach ($allShareAbout as $key => $value) {
				$resshare[$value['createtime']][$value['landuserid']] = $value;
				$datesharearr[] = $value['createtime'];
			}
			for ($i=0; $i < $days; $i++) { 
				$date = date("Y-m-d",strtotime("+".$i."day",$startSec));
				if (!in_array($date,$datearr)) {
					$countInfo[$i]['createtime'] = $date;
					$countInfo[$i]['counts'] = 0;
				}else{
					$countInfo[$i]['createtime'] = $date;
					$countInfo[$i]['counts'] = count($res[$date]);
				}
				if (!in_array($date,$datesharearr)) {
					$countInfo[$i]['sharecounts'] = 0;
				}else{
					$countInfo[$i]['sharecounts'] = count($resshare[$date]);
				}
			}
			//我的分享
			$nono['belongid'] = $info['pusherId'];
			$nono['xz_landuser.createtime'] = array('between',array($startTime,$endTime));
			$nono['groupid'] = array('NEQ',0);
			$nogroup = $this->landusers->where($nono)->select();
			//我没有分享
			$isnono['belongid'] = $info['pusherId'];
			$isnono['groupid'] = 0;
			$isnono['xz_landuser.createtime'] = array('between',array($startTime,$endTime));
			$isnogroup = $this->landusers->where($isnono)->select();
			//别人分享
			foreach ($allgroup as $key => $value) {
				if ($value != $info['pusherId']) {
					$tempt[] = $value;
				}
			}
			if (empty($tempt)) {
				$othershisnogroup = [];
			}else{
				$othersh['groupid'] = array('NEQ',0);
				$othersh['belongid'] = array('in',$tempt);
				$othersh['createtime'] = array('between',array($startTime,$endTime));
				$othershisnogroup = $this->landusers->where($othersh)->select();
			}
			$isall = count($allAbout);
			$mynocount = count($isnogroup);
			if ($isall == $mynocount) {
				$myshareCount = $mynocount;
				$otherShareCount = 0;
			}else{
				$myshareCount = count($nogroup);
				$otherShareCount = count($othershisnogroup);
			}
			$data['count']['allCount'] = count($allAbout);
			$data['count']['myshareCount'] = $myshareCount;
			$data['count']['otherShareCount'] = $otherShareCount;
			$data['count']['days'] = $days;
			$data['daysList'] = $countInfo;
			$code = 0;
			$message = "查询成功";
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];	
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 分享成果  用户搜索 actionType 1 全范围搜索 2 时间和名字搜索(传传要搜索日期当天的时间戳和名字)  3 时间搜索(传要搜索日期当天的时间戳)
	 * @return [type] [description]
	 */
	public function shareDetails()
	{
		$info = $this->registerApi();
		if (!empty($info['actionType']) && !empty($info['pusherId'])) {
			if (empty($info['endTime'])) {
				$endSec = time();
			}else{
				$endSec = $info['endTime'];
			}
			if (empty($info['days'])) {
				$days = 1;
			}else{
				$days = $info['days'];
			}
			$startSec = $endSec - (($days-1)*24*3600);
			$endTime = date('Y-m-d 23:59:59',$endSec);
			$startTime = date('Y-m-d 00:00:00',$startSec);
			$typeinfo = $this->typeinfo->select();
			foreach ($typeinfo as $key => $value) {
				$typearr[$value['id']] = $value['name'];
			}
			$manageinfo = $this->manageinfo->select();
			foreach ($manageinfo as $key => $value) {
				$managearr[$value['id']] = $value['name'];
			}
			$developmentinfo = $this->development->select();
			foreach ($developmentinfo as $key => $value) {
				$developmentarr[$value['id']] = $value['name'];
			}
			$groupname = $this->groups->field('groupid,name')->select();
			foreach ($groupname as $key => $value) {
				$grouparr[$value['groupid']] = $value['name'];
			}
			$sexarr = [
				'0'=>'男',
				'1'=>'女'
			];
			if ($info['actionType'] == 1) {
				if (!isset($info['search'])) {
					$code = -2001;
					$message = '请输入要搜索的名字或手机号';
					$data = [];
				}else{
					$search = $info['search'];
					$name['xz_landuser.username'] = $search;
					$name['xz_landuser.phone'] = $search;
					$name['_logic'] = 'or';
					$allno['_complex'] = $name;
					$allno['xz_landuser.groupid'] = array('NEQ',0);
					$temp = $this->landusers->join('xz_pusher ON xz_landuser.belongid = xz_pusher.id')
					->field('xz_pusher.id as pusherid,xz_pusher.name as pushersname,xz_landuser.groupid,xz_landuser.username as landusername,xz_landuser.sex,xz_landuser.typeid,xz_landuser.phone,xz_landuser.province,xz_landuser.city,xz_landuser.area,xz_landuser.address,xz_landuser.manageid,xz_landuser.development,xz_landuser.content,xz_landuser.createtime,xz_landuser.viewuser')
					->where($allno)
					->order('xz_landuser.createtime desc')
					->select();
					// $data['test']['temp'] = $temp;
					if (!empty($temp)) {
						foreach ($temp as $key => $value) {
							if (in_array($info['pusherId'],json_decode($value['viewuser'],true)) || ($temp[$key]['groupid'] == 0 && $temp[$key]['groupid'] == $info['pusherId'])) {
								$temp[$key]['typename'] = $typearr[$value['typeid']];
								$temp[$key]['sexname'] = $sexarr[$value['sex']];
								foreach (json_decode($value['manageid'],true) as $k => $v) {
									// $temp[$key]['managename'][$v] = $managearr[$v];
									$temp[$key]['manageInfo'][$k]['manageid'] = $v;
									$temp[$key]['manageInfo'][$k]['managename'] = $managearr[$v];
								}
								foreach (json_decode($value['development'],true) as $k => $v) {
									// $temp[$key]['developmentname'][$v] = $developmentarr[$v];
									$temp[$key]['developmentInfo'][$k]['developmentid'] = $v;
									$temp[$key]['developmentInfo'][$k]['developmentname'] = $developmentarr[$v];
								}
								$temp[$key]['groupname'] = $grouparr[$value['groupid']];
								$data[$key]['pusherid'] = $temp[$key]['pusherid'];
								$data[$key]['pushername'] = $temp[$key]['pushersname'];
								$data[$key]['groupid'] = $temp[$key]['groupid'];
								$data[$key]['groupname'] = $temp[$key]['groupname'];
								$data[$key]['landusername'] = $temp[$key]['landusername'];
								$data[$key]['sexid'] = $temp[$key]['sex'];
								$data[$key]['sexname'] = $temp[$key]['sexname'];
								$data[$key]['typeid'] = $temp[$key]['typeid'];
								$data[$key]['typename'] = $temp[$key]['typename'];
								$data[$key]['landuserphone'] = $temp[$key]['phone'];
								$data[$key]['province'] = $temp[$key]['province'];
								$data[$key]['city'] = $temp[$key]['city'];
								$data[$key]['area'] = $temp[$key]['area'];
								$data[$key]['address'] = $temp[$key]['address'];
								$data[$key]['content'] = json_decode($temp[$key]['content'],true);
								$data[$key]['createtime'] = $temp[$key]['createtime'];
								$data[$key]['manageInfo'] = $temp[$key]['manageInfo'];
								$data[$key]['developmentInfo'] = $temp[$key]['developmentInfo'];
							}
						}
					}else{
						$data = [];
					}
					$code = 0;
					$message = '成功';
				}
			}else if($info['actionType'] == 2){
				if (isset($info['search']) && isset($info['currentSec'])) {
					$search = $info['search'];
					$currentDate = date('Y-m-d',$info['currentSec']);
					$name['xz_landuser.username'] = $search;
					$name['xz_landuser.phone'] = $search;
					$name['_logic'] = 'or';
					$allno['_complex'] = $name;
					$allno['xz_landuser.groupid'] = array('NEQ',0);
					$allno['xz_landuser.createtime'] = $currentDate;
					$temp = $this->landusers->join('xz_pusher ON xz_landuser.belongid = xz_pusher.id')
					->field('xz_pusher.id as pusherid,xz_pusher.name as pushersname,xz_landuser.groupid,xz_landuser.username as landusername,xz_landuser.sex,xz_landuser.typeid,xz_landuser.phone,xz_landuser.province,xz_landuser.city,xz_landuser.area,xz_landuser.address,xz_landuser.manageid,xz_landuser.development,xz_landuser.content,xz_landuser.createtime,xz_landuser.viewuser')
					->where($allno)
					->order('xz_landuser.createtime desc')
					->select();
					if (!empty($temp)) {
						foreach ($temp as $key => $value) {
							if (in_array($info['pusherId'],json_decode($value['viewuser'],true)) || ($temp[$key]['groupid'] == 0 && $temp[$key]['groupid'] == $info['pusherId'])) {
								$temp[$key]['typename'] = $typearr[$value['typeid']];
								$temp[$key]['sexname'] = $sexarr[$value['sex']];
								foreach (json_decode($value['manageid'],true) as $k => $v) {
									// $temp[$key]['managename'][$v] = $managearr[$v];
									$temp[$key]['manageInfo'][$k]['manageid'] = $v;
									$temp[$key]['manageInfo'][$k]['managename'] = $managearr[$v];
								}
								foreach (json_decode($value['development'],true) as $k => $v) {
									// $temp[$key]['developmentname'][$v] = $developmentarr[$v];
									$temp[$key]['developmentInfo'][$k]['developmentid'] = $v;
									$temp[$key]['developmentInfo'][$k]['developmentname'] = $developmentarr[$v];
								}
								$temp[$key]['groupname'] = $grouparr[$value['groupid']];
								$data[$key]['pusherid'] = $temp[$key]['pusherid'];
								$data[$key]['pushername'] = $temp[$key]['pushersname'];
								$data[$key]['groupid'] = $temp[$key]['groupid'];
								$data[$key]['groupname'] = $temp[$key]['groupname'];
								$data[$key]['landusername'] = $temp[$key]['landusername'];
								$data[$key]['sexid'] = $temp[$key]['sex'];
								$data[$key]['sexname'] = $temp[$key]['sexname'];
								$data[$key]['typeid'] = $temp[$key]['typeid'];
								$data[$key]['typename'] = $temp[$key]['typename'];
								$data[$key]['landuserphone'] = $temp[$key]['phone'];
								$data[$key]['province'] = $temp[$key]['province'];
								$data[$key]['city'] = $temp[$key]['city'];
								$data[$key]['area'] = $temp[$key]['area'];
								$data[$key]['address'] = $temp[$key]['address'];
								$data[$key]['content'] = json_decode($temp[$key]['content'],true);
								$data[$key]['createtime'] = $temp[$key]['createtime'];
								$data[$key]['manageInfo'] = $temp[$key]['manageInfo'];
								$data[$key]['developmentInfo'] = $temp[$key]['developmentInfo'];
							}
						}
					}else{
						$data = [];
					}
					$code = 0;
					$message = '成功';
				}else{
					$code = -2001;
					$message = '传入要搜索的时间戳或内容';
					$data = [];
					
				}
					
			}else{
				if (isset($info['currentSec'])) {
					$currentDate = date('Y-m-d',$info['currentSec']);
					$allno['xz_landuser.createtime'] = $currentDate;
					$allno['xz_landuser.groupid'] = array('NEQ',0);
					$temp = $this->landusers->join('xz_pusher ON xz_landuser.belongid = xz_pusher.id')
					->field('xz_pusher.id as pusherid,xz_pusher.name as pushersname,xz_landuser.groupid,xz_landuser.username as landusername,xz_landuser.sex,xz_landuser.typeid,xz_landuser.phone,xz_landuser.province,xz_landuser.city,xz_landuser.area,xz_landuser.address,xz_landuser.manageid,xz_landuser.development,xz_landuser.content,xz_landuser.createtime,xz_landuser.viewuser')
					->where($allno)
					->select();
					if (!empty($temp)) {
						foreach ($temp as $key => $value) {
							if (in_array($info['pusherId'],json_decode($value['viewuser'],true)) || ($temp[$key]['groupid'] == 0 && $temp[$key]['groupid'] == $info['pusherId'])) {
								$temp[$key]['typename'] = $typearr[$value['typeid']];
								$temp[$key]['sexname'] = $sexarr[$value['sex']];
								foreach (json_decode($value['manageid'],true) as $k => $v) {
									// $temp[$key]['managename'][$v] = $managearr[$v];
									$temp[$key]['manageInfo'][$k]['manageid'] = $v;
									$temp[$key]['manageInfo'][$k]['managename'] = $managearr[$v];
								}
								foreach (json_decode($value['development'],true) as $k => $v) {
									// $temp[$key]['developmentname'][$v] = $developmentarr[$v];
									$temp[$key]['developmentInfo'][$k]['developmentid'] = $v;
									$temp[$key]['developmentInfo'][$k]['developmentname'] = $developmentarr[$v];
								}
								$temp[$key]['groupname'] = $grouparr[$value['groupid']];
								$data[$key]['pusherid'] = $temp[$key]['pusherid'];
								$data[$key]['pushername'] = $temp[$key]['pushersname'];
								$data[$key]['groupid'] = $temp[$key]['groupid'];
								$data[$key]['groupname'] = $temp[$key]['groupname'];
								$data[$key]['landusername'] = $temp[$key]['landusername'];
								$data[$key]['sexid'] = $temp[$key]['sex'];
								$data[$key]['sexname'] = $temp[$key]['sexname'];
								$data[$key]['typeid'] = $temp[$key]['typeid'];
								$data[$key]['typename'] = $temp[$key]['typename'];
								$data[$key]['landuserphone'] = $temp[$key]['phone'];
								$data[$key]['province'] = $temp[$key]['province'];
								$data[$key]['city'] = $temp[$key]['city'];
								$data[$key]['area'] = $temp[$key]['area'];
								$data[$key]['address'] = $temp[$key]['address'];
								$data[$key]['content'] = json_decode($temp[$key]['content'],true);
								$data[$key]['createtime'] = $temp[$key]['createtime'];
								$data[$key]['manageInfo'] = $temp[$key]['manageInfo'];
								$data[$key]['developmentInfo'] = $temp[$key]['developmentInfo'];
							}
						}
					}else{
						$data = [];
					}
					$code = 0;
					$message = '成功';
				}else{
					$code = -2001;
					$message = '缺少时间戳';
					$data = [];
				}
			}
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];	
		}
		$this->databack($code,$message,$data);
	}
	/**
	 * 手机验证码
	 * @return [type] [description]
	 */
	public function sendverifycode() {
		$info = $this->registerApi();
		if (!empty($info['phone'])) {
			$tel = $info['phone'];
			vendor('sendmsg');
			$obj = new \SendMsgService();
			$yzm = $obj::GetfourStr(4);
			$res = $obj::sendMsg($tel,$yzm);
			$info = json_decode($res,true);
			$content = array('code'=>$info['code'],'yzm'=>$yzm);
			$data = $content;
			$code =0;
			$message = '成功';
		}else{
			$code = -1001;
			$message = '传参错误';
			$data = [];	
		}
		$this->databack($code,$message,$data);
	}





	public function databack($code,$message='',$data=array())
    {
        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        );
        echo json_encode($result,JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function registerApi() {
		
		$data = file_get_contents('php://input');
		$info = json_decode($data,true);
		foreach ($info as $key => $value) {
			$info[$key] = trim($value);
		}
		return $info;
	}
	public function registerArrApi() {
		
		$data = file_get_contents('php://input');
		$info = json_decode($data,true);
		return $info;
	}


	/*高德地图根据经纬度查地点，范围radius=500*/
	public function GetRealgeo(){
		$str = [113.631805,34.749187];
		$para = "adc2e98502db81950e7206b62c12229f";
	     $url="http://restapi.amap.com/v3/geocode/regeo?output=json&location=".$str."&key=".$para."&radius=500&extensions=base";
	 
	     $content = file_get_contents($url);
		 $arr = json_decode($content,true);
		
		 $data = array(
			 "err_code" => $arr['infocode'], //错误编码，如出现错误可去高德地图查询原因
		     "address" => $arr['regeocode']['formatted_address'],
			 "country" => $arr['regeocode']['addressComponent']['country'],
			 "province" => $arr['regeocode']['addressComponent']['province'],
			 "city" => $arr['regeocode']['addressComponent']['city'],
			 "district" => $arr['regeocode']['addressComponent']['district'],
			 "township" => $arr['regeocode']['addressComponent']['township'],
			 "citycode" => $arr['regeocode']['addressComponent']['citycode'],
			 "adcode" => $arr['regeocode']['addressComponent']['adcode']
			 
		 );
		 dump($data);exit;
		 // return $data;
		 //测试OK
	}
	// public function getlocation()
	// {
	// 	$signPackage = $this->getSignPackage();
	// 	// dump($signPackage);
	// 	$this->assign('signPackage',$signPackage);
	// 	$this->display();
	// }
}