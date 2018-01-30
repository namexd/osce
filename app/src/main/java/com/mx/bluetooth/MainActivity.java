package com.mx.bluetooth;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Set;

import com.android.volley.Request.Method;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.Response.Listener;
import com.google.gson.Gson;
import com.identity.Shell;
import com.identity.Util;
import com.identity.globalEnum;
import com.mx.bluetooth.adapter.GridViewAdapter;
import com.mx.bluetooth.bean.CurrentGroupBean;
import com.mx.bluetooth.bean.CurrentGroupStudentBean;
import com.mx.bluetooth.bean.DrawInfor;
import com.mx.bluetooth.bean.EndExamBean;
import com.mx.bluetooth.bean.NextGroupBean;
import com.mx.bluetooth.bean.PollingStudentBean;
import com.mx.bluetooth.bean.PollingStudentInfo;
import com.mx.bluetooth.bean.StartExamBean;
import com.mx.bluetooth.custom.LoadingDialog;
import com.mx.bluetooth.fragment.FragmentDrawSuccess;
import com.mx.bluetooth.fragment.FragmentDrawTips;
import com.mx.bluetooth.fragment.FragmentReady;
import com.mx.bluetooth.service.UploadScroeService;
import com.mx.bluetooth.service.UploadScroeService.OnProgressListener;
import com.mx.bluetooth.util.Constant;
import com.mx.bluetooth.util.GsonRequest;
import com.mx.bluetooth.util.Utils;
import android.app.AlertDialog;
import android.app.FragmentManager;
import android.bluetooth.BluetoothAdapter;
import android.bluetooth.BluetoothDevice;
import android.content.BroadcastReceiver;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.os.Bundle;
import android.os.Handler;
import android.os.IBinder;
import android.os.Message;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.EditText;
import android.widget.GridView;
import android.widget.ImageButton;
import android.widget.TextView;
import android.widget.Toast;

/** 抽签，开始考试 */
public class MainActivity extends BaseActivity {

	// 当前考站
	private TextView mTextTitleStationName;

	// 当前时间
	private TextView mTextTitleTime;

	private TextView mTextVideo;

	private TextView mTextCancel;

	// 下一组
	private TextView mNextGroup;

	// 刷新
	private TextView mRefreshStudent, mRefreshCurrent;

	// 是否刷新我的考生
	private boolean isRefrushStudent = true;

	// 是否刷新当前小组
	private boolean isRefrushGroup = true;

	// 当前小组考生信息
	private GridView mCurrentGroup;

	// grid适配器
	private GridViewAdapter mAdapter;

	// grid数据源
	private ArrayList<CurrentGroupStudentBean> mStuList;

	private FragmentManager mFgManager;

	// 准备抽签碎片
	private FragmentReady mDrawReady;

	// 抽签成功碎片
	private FragmentDrawSuccess mDrawSuccess;

	// 抽签提示碎片
	private FragmentDrawTips mDrawTips;

	private ChangeUiReciver mChangeUiReciver;

	private long firstTime = 0;

	private ServiceConnection conn;// 异步上传成绩ServiceConnection

	private UploadScroeService msgService;// 异步上传成绩Service

	private Intent intent;// 启动上传成绩Service的intent

	private LoadingDialog dialog;

	private Shell shell=null;

	private BluetoothAdapter mbAdapter;

	private BluetoothDevice mDevice=null;

	private static final int REQUEST_ENABLE_BT = 2;

	private boolean bStop = false;

	private int m_sec1,m_sec2;
	private int m_msec1,m_msec2;
	private Calendar c;

	@Override
	protected void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);

		setContentView(R.layout.activity_main);

		this.getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
		//注册、初始化蓝牙设备
		if(BaseActivity.bConnected){

		}else {
			registerBluetooth();
		}
		findWidget();
		startUploadScroeService();
		new GetDataThread().start();
	}

	protected void registerBluetooth(){
		mbAdapter = BluetoothAdapter.getDefaultAdapter();
		if (mbAdapter == null) {
			Log.i("bluetooth", "mAdapter is null!===========================================");
		}
		if (!mbAdapter.isEnabled()) {
			Intent enableBtIntent = new Intent(BluetoothAdapter.ACTION_REQUEST_ENABLE);
			startActivityForResult(enableBtIntent, REQUEST_ENABLE_BT);
		}
		c = Calendar.getInstance();
		m_sec1 = c.get(Calendar.SECOND);
		m_msec1 = c.get(Calendar.MILLISECOND);
		Set<BluetoothDevice> pairedDevices = mbAdapter.getBondedDevices();
		if (pairedDevices.size() > 0) {
			for (BluetoothDevice device : pairedDevices) {
				String str;
				Log.i(TAG,device.getName()+"====================================");
				if(device.getName().length()<3){
					str=device.getName();
				}else {
					str = device.getName().substring(0, 3);
				}
				Log.w("pairedDevices", "device.getName().substring(0, 1) is:" + str);
				if ((str.equalsIgnoreCase("SYN")) || (str.equalsIgnoreCase("SS-"))) {
					Log.w("onCreate", "device.getName() is SYNTHESIS");
					mDevice = device;
				} else   //是否能进入Else
				{
					Log.w("onCreate", "device.getName() is not SYNTHESIS");
					boolean bAllNum = false;
					if (device.getName().length() > 9) {
						str = device.getName().substring(0, 10);
						bAllNum = str.matches("[0-9]+");
						if (bAllNum == true) {
							mDevice = device;
						}
					}
				}
				Log.i("bluetooth", device.getName() + "====" + device.getAddress());
			}
			try {
				mbAdapter.cancelDiscovery();
				if (mDevice != null)
					shell = new Shell(mScontext, mDevice);
				c = Calendar.getInstance();
				m_sec2 = c.get(Calendar.SECOND);
				m_msec1 = c.get(Calendar.MILLISECOND);
				int d = m_sec2 - m_sec1;
				int md = m_msec2 - m_msec1;
				if (d < 0)
					d = d + 60;
				if (md < 0)
					md = md + 1000;
				//	Toast("connect timeee is:  "+d+"."+md+"s");
				if (shell.Register())
				{
					//0316 btnRegist.setEnabled(false);
					Toast("取机具编号成功！");
					globalEnum ge = shell.Init();
					if (ge == globalEnum.INITIAL_SUCCESS) {
						BaseActivity.bInitial = true;
						Toast("建立连接成功！");
						BaseActivity.bConnected = true;

					} else {
						shell.EndCommunication();//0316
						Log.i(TAG,"Init失败,与机具建立连接失败，请检查蓝牙配置");
					}
				}else
				{
					Log.i(TAG,"Register失败,与机具建立连接失败，请检查蓝牙配置");
				}
			} catch (Exception e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
				Log.w("test", "Socket connect error！");
				Toast("与机具建立连接失败，请尝试重新启动应用程序!");
			}
			Log.w("test", "Socket connect OK！");
		}
	}

	class GetDataThread extends Thread{
		private String data ;
		private byte[] cardInfo = new byte[256];
		private int count = 0 ;
		private Message msg;
		private String wltPath="";
		private String termBPath="";
		private boolean bRet = false;

		@Override
		public void run() {
			globalEnum ge = globalEnum.GetIndentiyCardData_GetData_Failed;
			try {
				Thread.sleep(2000);

				globalEnum gFindCard = globalEnum.NONE;
				long start = System.currentTimeMillis();
				while (!bStop) {
					data = null;//
					msg = handler.obtainMessage(71, data);//发送消息
					handler.sendMessage(msg);
					bRet = shell.SearchCard();
					if (bRet) {
						data = null;//
						msg = handler.obtainMessage(1, data);//发送消息
						handler.sendMessage(msg);
						bRet = shell.SelectCard();
						if(bRet){
							data = null;//
							msg = handler.obtainMessage(2, data);//发送消息
							handler.sendMessage(msg);
							//Thread.sleep(100);

							int nRet = shell.ReadCardWithFinger();//nRet 1:二代证无指纹 2：二代证包含指纹
							if (nRet>0) {
								data = null;//
								byte[] fingerData = new byte[1024];
								if(nRet == 2){
									bRet = shell.GetFingerData(fingerData);
									Log.w("ComShell","fingerData is:"+ Util.toHexStringNoSpace(fingerData, 1024));
								}

								msg = handler.obtainMessage(3, data);//发送消息
								handler.sendMessage(msg);

								cardInfo = shell.GetCardInfoBytes();
								if(shell.GetCardTypeFlag(cardInfo)==0){//0时为二代证信息
//									data = String.format(
//											"姓名：%s 性别：%s 民族：%s 出生日期：%s 住址：%s 身份证号：%s 签发机关：%s 有效期：%s-%s",
//											shell.GetName(cardInfo), shell.GetGender(cardInfo), shell.GetNational(cardInfo),
//											shell.GetBirthday(cardInfo), shell.GetAddress(cardInfo),
//											shell.GetIndentityCard(cardInfo), shell.GetIssued(cardInfo),
//											shell.GetStartDate(cardInfo), shell.GetEndDate(cardInfo));
									data = shell.GetIndentityCard(cardInfo);
								}else if(shell.GetCardTypeFlag(cardInfo)==1){//1时为外国人居住证信息
									data = String.format(
											"英文名字：%s 中文名字：%s 性别：%s 国籍代码：%s 国籍名称：%s 出生日期：%s 身份证号：%s 签发机关：%s 有效期：%s-%s",
											shell.GetFEName(cardInfo),shell.GetFCName(cardInfo), shell.GetFGender(cardInfo),  shell.GetFCountryCode(cardInfo),shell.GetFCountryName(cardInfo),
											shell.GetFBirthday(cardInfo),
											shell.GetFIndentityCard(cardInfo), shell.GetFIssued(cardInfo),
											shell.GetFStartDate(cardInfo), shell.GetFEndDate(cardInfo));
								}else{
									msg = handler.obtainMessage(6, data);//读卡失败
									handler.sendMessage(msg);	//readC
									break;
								}
								msg = handler.obtainMessage(0, data);//发送消息
								handler.sendMessage(msg);

								//	Log.w("777"," shell.GetEndDate(cardInfo) is:"+ shell.GetEndDate(cardInfo));

								// 没有模块号，所以屏蔽
								wltPath="/data/data/com.testjar/files/";
								termBPath="/mnt/sdcard/";
								int nret = shell.GetPic(wltPath,termBPath);

								//wltPath = getFilesDir().getPath()+ "/";
								//int nret = shell.GetPic(wltPath);
								Log.w("ComShell","old GetPic nRet is:"+nret);
								if(nret > 0)
								{
									//Bitmap bm = BitmapFactory.decodeFile(Environment.getExternalStorageDirectory()+"/sdseslib/" + "zp.bmp");
									Bitmap bm = BitmapFactory.decodeFile("/data/data/com.testjar/files/zp.bmp");
									msg = handler.obtainMessage(100, bm);//发送消息
									handler.sendMessage(msg);

								}else if(nret == -5)
								{
									msg = handler.obtainMessage(101, data);//发送消息
									handler.sendMessage(msg);
								}else if(nret == -1)
								{
									msg = handler.obtainMessage(102, data);//发送消息
									handler.sendMessage(msg);
								}
								//break;//0316  调试用，所以增加
							}else{
								msg = handler.obtainMessage(6, data);//发送消息
								handler.sendMessage(msg);	//readCard error
							}
						}else{
							msg = handler.obtainMessage(5, data);//发送消息
							handler.sendMessage(msg);	//selectCard error
						}
					}else{
						msg = handler.obtainMessage(4, data);//发送消息
						handler.sendMessage(msg);	//searchCard error
					}
					Thread.sleep(50);
				}
			} catch (IOException e) {
				e.printStackTrace();
			} catch (InterruptedException e) {
				e.printStackTrace();
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
	}

	protected Handler handler = new Handler(){
		private String data;
		private Bitmap bm;
		private int t_sec1,t_sec2;
		private int t_msec1,t_msec2;

		private Calendar t;

		@Override
		public void handleMessage(Message msg) {
			switch (msg.what) {
				case 0:
					data = (String) msg.obj;
					if(data == null){
					}else {
						//mInfoView.clear();
						t = Calendar.getInstance();
						t_sec2 = t.get(Calendar.SECOND);
						t_msec2 = t.get(Calendar.MILLISECOND);
						int d = t_sec2-t_sec1;
						int md = t_msec2-t_msec1;
						if(d<0)
							d = d + 60;
						if(md<0){
							d = d -1;
							md = md + 1000;
						}
						Log.i(TAG,"readcard time is:  "+d+"."+md+"s");
						Log.i(TAG,data);
//						Toast(data);
						sendDrawRequest(data,Utils.getSharedPrefrences(MainActivity.this, "room_id"),
								Utils.getSharedPrefrences(MainActivity.this, "user_id"),0);
					}
					break;
				case 71:
					t = Calendar.getInstance();
					t_sec1 = t.get(Calendar.SECOND);
					t_msec1 = t.get(Calendar.MILLISECOND);
					break;
				case 100:
					bm = (Bitmap) msg.obj;
//					iv.setImageBitmap(bm);

					deleteFile("zp.bmp");

					break;
				case 101:
					Toast("照片解码授权文件不正确");
					break;
				case 102:
					Log.i(TAG,"照片原始数据不正确");
					break;
				case 1:
//					iv.setImageBitmap(null);
					//Toast("SearchCard ok"); 
					break;
				case 4:
//			Toast("SearchCard error");
					//Toast("正在寻卡...");
					break;
				case 5:
					Toast("SelectCard error");
					break;
				case 6:
					Toast("ReadCard error");
					break;
				case 87:
					Toast("读卡初始化中，请稍候...");
					break;
				case 88:
					Toast("机具信息监听中...");
					break;
				case 99:
//					iv.setImageBitmap(null);
					break;
				case 110:
					if (mDrawSuccess != null) {

						// mDrawSuccess = (FragmentDrawSuccess)
						// manager.findFragmentByTag("success");
						mFgManager.beginTransaction().replace(R.id.fragment_draw, mDrawSuccess, "success").commit();

					} else {
						mDrawSuccess = new FragmentDrawSuccess();
						mFgManager.beginTransaction().replace(R.id.fragment_draw, mDrawSuccess, "success").commit();
					}
					break;
				default:
					break;
			}
		}
	};

	public void startUploadScroeService() {

		conn = new ServiceConnection() {

			@Override
			public void onServiceDisconnected(ComponentName name) {

			}

			@Override
			public void onServiceConnected(ComponentName name, IBinder service) {

				// 返回一个MsgService对象
				Log.v("MediaPlayerActivity", "返回");
				// 更新带上传成绩列表
				msgService = ((UploadScroeService.MsgBinder) service).getService();
				msgService.UpdateList();
				// 注册回调接口来接收下载进度的变化
				msgService.setOnProgressListener(new OnProgressListener() {

					@Override
					public void onProgress(int progress) {
						Log.v("MainActivity", progress + "");
					}
				});

			}
		};
		intent = new Intent(getApplicationContext(), UploadScroeService.class);
		bindService(intent, conn, Context.BIND_AUTO_CREATE);
		startService(intent);
	}

	@Override
	protected void onSaveInstanceState(Bundle outState) {
		// super.onSaveInstanceState(outState);
	}

	@Override
	protected void onStart() {
		super.onStart();

		IntentFilter filter = new IntentFilter();// 注册耳机插孔广播
		filter.addAction(Intent.ACTION_HEADSET_PLUG);
		registerReceiver(mHeadsetPlugReceiver, filter);

		IntentFilter changUiFilter = new IntentFilter();// 注册ChangeUi广播
		changUiFilter.addAction(Constant.ACTION_CHANGE_CURRENT_GROUP);
		changUiFilter.addAction(Constant.ACTION_CHANGE_CURRENT_STUDENT);
		changUiFilter.addAction(Constant.ACTION_CHANGE_NEXT_GROUP);
		changUiFilter.addAction(Constant.ACTION_START_NFC_READER);
		changUiFilter.addAction(Constant.ACTION_THEORY_EXAM_BEGIN);
		changUiFilter.addAction(Constant.ACTION_THEORY_EXAM_END);
		changUiFilter.addAction(Constant.ACTION_REFRESH);
		registerReceiver(mChangeUiReciver, changUiFilter);

		getCurrentGroupMessage();
	}

	@Override
	protected void onStop() {
		super.onStop();

		unregisterReceiver(mHeadsetPlugReceiver);// 注销耳机插孔广播
		unregisterReceiver(mChangeUiReciver);

	}

	@Override
	protected void onDestroy() {
		Log.v("MediaPlayerActivity", "MediaPlayerActivity onDestroy");
		unbindService(conn);
		super.onDestroy();
	}

	private final BroadcastReceiver mHeadsetPlugReceiver = new BroadcastReceiver() {

		@Override
		public void onReceive(Context context, Intent intent) {

		}
	};


	@Override
	public void onClick(View v) {

	}

	/** 初始化视图控件 */
	private void findWidget() {
		// 添加初始碎片
		mDrawReady = new FragmentReady();

		mFgManager = getFragmentManager();

		if (mFgManager.findFragmentById(R.id.fragment_draw) == null) {

			mFgManager.beginTransaction().add(R.id.fragment_draw, mDrawReady, "wait").commit();
		} else {
			mFgManager.beginTransaction().replace(R.id.fragment_draw, mDrawReady, "wait").commit();
		}

		mChangeUiReciver = new ChangeUiReciver();

		// 站名
		mTextTitleStationName = (TextView) findViewById(R.id.textView_testStation);
		mTextTitleStationName.setText(Utils.getSharedPrefrences(MainActivity.this, "station_name"));

		// 限制时间
		mTextTitleTime = (TextView) findViewById(R.id.textView_testTime);
		mTextTitleTime.setText("限时:" + Utils.getSharedPrefrences(MainActivity.this, "exam_LimitTime") + "分钟");

		// 实时视频
		mTextVideo = (TextView) findViewById(R.id.tv_vdieo);
		mTextVideo.setVisibility(View.VISIBLE);
		mTextVideo.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {

				startActivity(new Intent(MainActivity.this, VideoActivity.class));
			}
		});

		// 初始化隐藏我的考生刷新按钮
		mRefreshStudent = (TextView) findViewById(R.id.refrushStudent);
		mRefreshStudent.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {

				if (isRefrushStudent) {

					isRefrushStudent = false;

					refreshCurrentStudent(Utils.getSharedPrefrences(MainActivity.this, "station_id"),
							Utils.getSharedPrefrences(MainActivity.this, "exam_id"));

				} else {
					Toast("正在刷新，请等待...");
				}
			}

		});
		mRefreshStudent.setVisibility(View.GONE);

		// 刷新当前小组
		mRefreshCurrent = (TextView) findViewById(R.id.refrushCourrentGroup);
		mRefreshCurrent.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				if (isRefrushGroup) {
					isRefrushGroup = false;

					getCurrentGroupMessage();
				} else {
					Toast("正在刷新，请等待...");
				}
			}
		});

		// 下一组
		mNextGroup = (TextView) findViewById(R.id.texiView_nextGroup);

		// 当前组
		mStuList = new ArrayList<CurrentGroupStudentBean>();
		mCurrentGroup = (GridView) findViewById(R.id.gridView_currentGroup);
		mAdapter = new GridViewAdapter(MainActivity.this, mStuList);
		mCurrentGroup.setAdapter(mAdapter);

		// 去除黄色背景
		mCurrentGroup.setSelector(new ColorDrawable(Color.TRANSPARENT));

		mCurrentGroup.setOnItemLongClickListener(new AdapterView.OnItemLongClickListener() {

			@Override
			public boolean onItemLongClick(AdapterView<?> arg0, View arg1, final int arg2, long arg3) {
				// TODO Auto-generated method stub
				new AlertDialog.Builder(MainActivity.this,R.style.AlertDialog).
						setTitle("请选择").
						setItems(new String[]{"弃考","排到最后","手动输入身份证号"}, new DialogInterface.OnClickListener() {
							@Override
							public void onClick(DialogInterface dialog, int which) {
								switch (which){
									case 0:
										//弃考
										new AlertDialog.Builder(MainActivity.this,R.style.AlertDialog).setTitle("确认弃考？").setPositiveButton("确定", new DialogInterface.OnClickListener() {
											@Override
											public void onClick(DialogInterface dialog, int which) {
												qikao(arg2);
											}
										})
												.setNegativeButton("取消", null).show();
										break;
									case 1:
										//拍到最后
										new AlertDialog.Builder(MainActivity.this,R.style.AlertDialog).setTitle("确认排到最后？").setPositiveButton("确定", new DialogInterface.OnClickListener() {
											@Override
											public void onClick(DialogInterface dialog, int which) {
												paidaozuihou(arg2);
											}
										})
												.setNegativeButton("取消", null).show();
										break;
									case 2:
										//手动输入身份证号
										final EditText et = new EditText(MainActivity.this);
										et.setWidth(800);
										new AlertDialog.Builder(MainActivity.this,R.style.AlertDialog).setTitle("请输入身份证号").setView(et).setPositiveButton("确定", new DialogInterface.OnClickListener() {
											@Override
											public void onClick(DialogInterface dialog, int which) {
												sendDrawRequest(et.getText().toString(),Utils.getSharedPrefrences(MainActivity.this, "room_id"),
														Utils.getSharedPrefrences(MainActivity.this, "user_id"),1);
											}
										})
												.setNegativeButton("取消", null).show();
										break;
								}
							}
						}).show();
				return false;
			}
		});
		// 注销
		mTextCancel = (TextView)findViewById(R.id.tv_cancel);
		mTextCancel.setVisibility(View.VISIBLE);
		mTextCancel.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View arg0) {
				// TODO Auto-generated method stub
				new AlertDialog.Builder(MainActivity.this,R.style.AlertDialog).setTitle("确认注销？").setPositiveButton("确定", new DialogInterface.OnClickListener() {
					@Override
					public void onClick(DialogInterface dialog, int which) {
						Utils.deleteSharedPrefrences(MainActivity.this);
						Toast("注销成功");
						startActivity(new Intent(MainActivity.this,LoginActivity.class));
						finish();
					}
				})
						.setNegativeButton("取消", null).show();

			}
		});
	}

	protected void qikao(int arg2){
		if(mStuList.get(arg2).getStation_id()==null) {
			mStuList.get(arg2).setStation_id("0");
		}
		String qikaoUrl = BaseActivity.mSUrl + Constant.IGNORE +"?exam_queue_id="+mStuList.get(arg2).getExam_queue_id()
				+"&station_id="+mStuList.get(arg2).getStation_id()+ "&teacher_id="
				+ Utils.getSharedPrefrences(MainActivity.this, "user_id") +"&remove=1";

//		Map<String, String> params = new HashMap<String, String>();
//		params.put("exam_queue_id", String.valueOf(mStuList.get(arg2).getExam_queue_id()));
//		params.put("station_id", mStuList.get(arg2).getStation_id());
//		params.put("teacher_id", Utils.getSharedPrefrences(MainActivity.this, "user_id"));
//		params.put("remove", "1");
		Log.e(">>>QiKao Url<<<", qikaoUrl);

		try {
			GsonRequest<NextGroupBean> qikaoGet = new GsonRequest<NextGroupBean>(Method.GET, qikaoUrl,
					NextGroupBean.class, null, null, new Response.Listener<NextGroupBean>() {

				@Override
				public void onResponse(NextGroupBean arg0) {

					Log.e("arg0<<<<<",arg0.getCode()+"");

					if (String.valueOf(arg0.getCode()).equals("1")) {
						Toast("已做弃考处理");
						getCurrentGroupMessage();
					}
				}

			}, refreshErrorListener());

			executeRequest(qikaoGet);

		} catch (Exception e) {

			Toast("操作失败");
		}
	}

	protected void paidaozuihou(int arg2) {
		if(mStuList.get(arg2).getStation_id()==null) {
			mStuList.get(arg2).setStation_id("0");
		}
		String qikaoUrl = BaseActivity.mSUrl + Constant.IGNORE +"?exam_queue_id="+mStuList.get(arg2).getExam_queue_id()
				+"&station_id="+mStuList.get(arg2).getStation_id()+ "&teacher_id="
				+ Utils.getSharedPrefrences(MainActivity.this, "user_id");

		Log.e(">>>QiKao Url1<<<", qikaoUrl);

		try {
			GsonRequest<NextGroupBean> qikaoGet = new GsonRequest<NextGroupBean>(Method.GET, qikaoUrl,
					NextGroupBean.class, null, null, new Response.Listener<NextGroupBean>() {

				@Override
				public void onResponse(NextGroupBean arg0) {

					NextGroupBean nextGroup = arg0;

					if (String.valueOf(nextGroup.getCode()).equals("1")) {
						Toast("已将该考生排到最后考");
						getCurrentGroupMessage();
					}
				}
			}, refreshErrorListener());

			executeRequest(qikaoGet);

		} catch (Exception e) {

			Toast("操作失败");
		}
	}


	// 初始化ActionBar
	private void initActionBar() {
		ImageButton arrowImageBtn = (ImageButton) findViewById(R.id.image_arrow);
		arrowImageBtn.setVisibility(View.VISIBLE);
		arrowImageBtn.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {

				long clickTime = System.currentTimeMillis();

				if (clickTime - firstTime > 2000) {
					firstTime = clickTime;
					Toast.makeText(MainActivity.this, "再次点击退出程序", Toast.LENGTH_SHORT).show();
				} else {
					Utils.deleteSharedPrefrences(MainActivity.this);
					mBaseApp.exit();// 退出App
				}
			}
		});
	}

	// 手动刷新当前考生
	private void refreshCurrentStudent(String station_id, String exam_id) {

		HashMap<String, String> params = new HashMap<String, String>();

		if (station_id != null && station_id.trim().length() > 0) {
			params.put("station_id", station_id);
		} else {
			Toast("请求当前考生参数：station_id有误");
			isRefrushStudent = true;
			return;
		}
		if (exam_id != null && exam_id.trim().length() > 0) {
			params.put("exam_id", exam_id);
		} else {
			Toast("请求当前考生参数：exam_id有误");
			isRefrushStudent = true;
			return;
		}
		String refreshUrl = BaseActivity.mSUrl + Constant.REFRESH;

		Log.e(">>>refreshUrl<<<", refreshUrl);

		try {
			openProgressDialog();
			GsonRequest<PollingStudentInfo> refreshRequest = new GsonRequest<>(Method.POST, refreshUrl,
					PollingStudentInfo.class, null, params, new Listener<PollingStudentInfo>() {

						@Override
						public void onResponse(PollingStudentInfo currentStudent) {

							PollingStudentBean pollingStudent = null;

							isRefrushStudent = true;

							if (currentStudent.getCode() == 1) {

								boolean isChange = MainActivity.this.getFragmentManager()
										.findFragmentById(R.id.fragment_draw) instanceof FragmentDrawSuccess;

								if (!isChange) {// 如果当前界面不是抽签成功的界面，替换
									pollingStudent = currentStudent.getData();
									Utils.saveSharedPrefrences(MainActivity.this, "student_name",
											pollingStudent.getName());
									Utils.saveSharedPrefrences(MainActivity.this, "student_code",
											pollingStudent.getCode());
									Utils.saveSharedPrefrences(MainActivity.this, "student_idcard",
											pollingStudent.getIdcard());
									Utils.saveSharedPrefrences(MainActivity.this, "student_mobile",
											pollingStudent.getMobile());
									Utils.saveSharedPrefrences(MainActivity.this, "student_avator",
											pollingStudent.getAvator());
									Utils.saveSharedPrefrences(MainActivity.this, "student_status",
											pollingStudent.getStatus() + "");
									Utils.saveSharedPrefrences(MainActivity.this, "student_id",
											pollingStudent.getStudent_id() + "");
									Utils.saveSharedPrefrences(MainActivity.this, "student_exam_sequence",
											pollingStudent.getExam_sequence());
									Utils.saveSharedPrefrences(MainActivity.this, "student_exam_queue_id",
											pollingStudent.getExam_queue_id() + "");
									Utils.saveSharedPrefrences(MainActivity.this, "controlMark",
											pollingStudent.getControlMark()+"");
									Utils.saveSharedPrefrences(MainActivity.this, "reason",
											pollingStudent.getReason() +"");

									handler.sendEmptyMessageDelayed(110,1000);
								}

							} else {
								Toast("当前暂无考生");
								//+ currentStudent.getCode()
							}
							closeProgressDialog();
						}
					}, refreshErrorListener());

			executeRequest(refreshRequest);

		} catch (Exception e) {
			isRefrushStudent = true;
			closeProgressDialog();
			Toast("当前考生数据格式有误");
		}

	}

	private Response.ErrorListener refreshErrorListener() {

		isRefrushStudent = true;

		isRefrushGroup = true;

		return new Response.ErrorListener() {
			@Override
			public void onErrorResponse(VolleyError error) {
				error.printStackTrace();
			}

		};
	}

	/** 获取下一组考生 信息，改变对应界面,onResume调用 */
	private void getNextGourpMessage() {

		String nextGroupUrl = BaseActivity.mSUrl + Constant.NEXT_GROUP + "?id="
				+ Utils.getSharedPrefrences(MainActivity.this, "user_id");

		Log.e(">>>Next Group Url<<<", nextGroupUrl);

		try {
			GsonRequest<NextGroupBean> nextGet = new GsonRequest<NextGroupBean>(Method.GET, nextGroupUrl,
					NextGroupBean.class, null, null, new Response.Listener<NextGroupBean>() {

						@Override
						public void onResponse(NextGroupBean arg0) {

							NextGroupBean nextGroup = arg0;

							isRefrushGroup = true;

							if (nextGroup.getCode() == 1) {
								if (arg0.getData() != null && arg0.getData().size() > 0) {

									StringBuffer nextGroupName = new StringBuffer();

									for (int i = 0; i < arg0.getData().size(); i++) {

										nextGroupName.append(arg0.getData().get(i).getStudent_name() + ",");
									}

									String names = nextGroupName.toString();

									mNextGroup.setText("下一组考生：" + names.substring(0, names.length() - 1));

								} else {
									mNextGroup.setText("下一组考生：没有考生");
								}
							} else if (arg0.getCode() == 3000) {

								mNextGroup.setText("当前没有正在进行的考试!");
							}
						}
					}, refreshErrorListener());

			executeRequest(nextGet);

		} catch (Exception e) {

			isRefrushGroup = true;

			Toast("下一考生小组获取失败");
		}
	}

	/** 获取当前考生小组的信息 */
	private void getCurrentGroupMessage() {

		String ncurrentGroupUrl = BaseActivity.mSUrl + Constant.CURRENT_GROUP + "?id="
				+ Utils.getSharedPrefrences(MainActivity.this, "user_id");

		Log.e(">>>Current Group Url<<<", ncurrentGroupUrl);

		try {
			GsonRequest<CurrentGroupBean> nextGet = new GsonRequest<CurrentGroupBean>(Method.GET, ncurrentGroupUrl,
					CurrentGroupBean.class, null, null, new Response.Listener<CurrentGroupBean>() {

						@Override
						public void onResponse(CurrentGroupBean arg0) {

							isRefrushGroup = true;

							if (arg0.getCode() == 1) {
								if (arg0.getData().size() > 0) {

									Utils.saveSharedPrefrences(MainActivity.this, "student_exam_queue_id",
											arg0.getData().get(0).getExam_queue_id() + "");

									getNextGourpMessage();

									mStuList.clear();

									mStuList.addAll((ArrayList<CurrentGroupStudentBean>) (arg0.getData()));

									mAdapter.notifyDataSetChanged();

								} else {
									mStuList.clear();
									mAdapter.notifyDataSetChanged();
								}
							}
						}
					}, refreshErrorListener());

			executeRequest(nextGet);
		} catch (Exception e) {
			isRefrushGroup = true;
			Toast("当前考生小组获取失败");
		}

	}

	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {

		if (keyCode == KeyEvent.KEYCODE_BACK) {
			Toast("您正在监考，请不要退出！");
		}
		return false;
	}

	public class ChangeUiReciver extends BroadcastReceiver {
		private String DataMessage = null;
		private String stationId = null;
		private String sequence_mode = null;
		private String room_id = null;
		private String teacher_id = null;
		private String exam_type = null;

		@Override
		public void onReceive(Context context, Intent intent) {
			DataMessage = intent.getStringExtra("Message");
			stationId = Utils.getSharedPrefrences(MainActivity.this, "station_id");// 考站id
			sequence_mode = Utils.getSharedPrefrences(MainActivity.this, "sequence_mode");// 排考模式
			room_id = Utils.getSharedPrefrences(MainActivity.this, "room_id");// 房间id
			teacher_id = Utils.getSharedPrefrences(MainActivity.this, "user_id");// 老师id
			exam_type = Utils.getSharedPrefrences(MainActivity.this, "type");// 考试类型

			switch (intent.getAction()) {

			case Constant.ACTION_REFRESH:

				mRefreshStudent.setVisibility(View.VISIBLE);

				break;

			case Constant.ACTION_START_NFC_READER:

				break;
			case Constant.ACTION_CHANGE_CURRENT_GROUP:// 当前考生小组
				CurrentGroupBean currentGroupBean = null;
				try {
					currentGroupBean = new Gson().fromJson(DataMessage, CurrentGroupBean.class);// 当前小组数据
					if (sequence_mode.equals(Constant.STATION) && stationId != null) {// 考站模式,考站id必不为null
						if (currentGroupBean.getData().size() > 0
								&& currentGroupBean.getData().get(0).getStation_id().equals(stationId)) {

							// 本地推送接受存储
							mStuList.clear();
							mStuList.addAll((ArrayList<CurrentGroupStudentBean>) (currentGroupBean.getData()));
							mAdapter.notifyDataSetChanged();
						}

					} else if (sequence_mode.equals(Constant.EXAMINATION) && room_id != null) {// 考场模式.房间id必不为null

						if (currentGroupBean.getData().size() > 0
								&& currentGroupBean.getData().get(0).getRoom_id().equals(room_id)) {

							mStuList.clear();

							mStuList.addAll((ArrayList<CurrentGroupStudentBean>) (currentGroupBean.getData()));

							mAdapter.notifyDataSetChanged();
						}
					}
				} catch (Exception e) {
					Toast("当前考生小组数据返回有误");
					break;
				}
				break;
			case Constant.ACTION_CHANGE_NEXT_GROUP:// 下一组考生
				NextGroupBean nexGroup = null;
				try {
					nexGroup = new Gson().fromJson(DataMessage, NextGroupBean.class);
					if (sequence_mode.equals(Constant.STATION) && stationId != null) {
						if (nexGroup.getData().size() > 0
								&& nexGroup.getData().get(0).getStation_id().equals(stationId)) {
							StringBuffer nextGroupName = new StringBuffer();
							for (int i = 0; i < nexGroup.getData().size(); i++) {
								nextGroupName.append(nexGroup.getData().get(i).getStudent_name() + ",");
							}
							String names = nextGroupName.toString();
							mNextGroup.setText("下一组考生：" + names.substring(0, names.length() - 1));
						}
					} else if (sequence_mode.equals(Constant.EXAMINATION) && room_id != null) {
						if (nexGroup.getData().size() > 0 && nexGroup.getData().get(0).getRoom_id().equals(room_id)) {
							StringBuffer nextGroupName = new StringBuffer();
							for (int i = 0; i < nexGroup.getData().size(); i++) {
								nextGroupName.append(nexGroup.getData().get(i).getStudent_name() + ",");
							}
							String names = nextGroupName.toString();
							mNextGroup.setText("下一组考生：" + names.substring(0, names.length() - 1));
						}
					}
				} catch (Exception e) {
					Toast("下一组考生小组数据返回有误");
					break;
				}
				break;
			case Constant.ACTION_CHANGE_CURRENT_STUDENT:// 当前考生

				boolean isChange = MainActivity.this.getFragmentManager()
						.findFragmentById(R.id.fragment_draw) instanceof FragmentDrawSuccess;
				PollingStudentInfo currentStudent = null;
				PollingStudentBean pollingStudent = null;
				try {
					if (!isChange) {
						
						currentStudent = new Gson().fromJson(DataMessage, PollingStudentInfo.class);
						int currentStudentTeacher = currentStudent.getData().getTeacher_id();// 监考老师Id
						String currentStudentStation = currentStudent.getData().getStation_id();// 考站Id
						if (currentStudentStation != null && currentStudentTeacher == Integer.parseInt(teacher_id)
								&& currentStudentStation.equals(stationId)) {
							pollingStudent = currentStudent.getData();
							Utils.saveSharedPrefrences(MainActivity.this, "student_name", pollingStudent.getName());
							Utils.saveSharedPrefrences(MainActivity.this, "student_code", pollingStudent.getCode());
							Utils.saveSharedPrefrences(MainActivity.this, "student_idcard", pollingStudent.getIdcard());
							Utils.saveSharedPrefrences(MainActivity.this, "student_mobile", pollingStudent.getMobile());
							Utils.saveSharedPrefrences(MainActivity.this, "student_avator", pollingStudent.getAvator());
							Utils.saveSharedPrefrences(MainActivity.this, "student_status",
									pollingStudent.getStatus() + "");
							Utils.saveSharedPrefrences(MainActivity.this, "student_id",
									pollingStudent.getStudent_id() + "");
							Utils.saveSharedPrefrences(MainActivity.this, "student_exam_sequence",
									pollingStudent.getExam_sequence());
							Utils.saveSharedPrefrences(MainActivity.this, "student_exam_queue_id",
									pollingStudent.getExam_queue_id() + "");
							//2016-6-17异常考生时添加
							Utils.saveSharedPrefrences(MainActivity.this, "controlMark",
									pollingStudent.getControlMark()+"");
							Utils.saveSharedPrefrences(MainActivity.this, "reason",
									pollingStudent.getReason() );

							handler.sendEmptyMessageDelayed(110,1000);
						}
					}
					break;
				} catch (Exception e) {
					Toast("后台当前考生数据返回有误");
					break;
				}
			case Constant.ACTION_THEORY_EXAM_BEGIN:// 理论考试开始且PC端学生开始考试
				boolean isBeginByPc = MainActivity.this.getFragmentManager()
						.findFragmentById(R.id.fragment_draw) instanceof FragmentDrawSuccess;
				StartExamBean startBean = null;
				String exam_screening_id = Utils.getSharedPrefrences(MainActivity.this, "exam_screening_id");
				String student_id = Utils.getSharedPrefrences(MainActivity.this, "student_id");

				try {
					startBean = new Gson().fromJson(DataMessage, StartExamBean.class);
					if (isBeginByPc && exam_type.equals(Constant.THEORY_STATION) && exam_screening_id != null
							&& student_id != null) {

						if (startBean.getData().getExam_screening_id().equals(exam_screening_id)
								&& startBean.getData().getStudent_id().equals(student_id)) {
							mDrawSuccess = (FragmentDrawSuccess) getFragmentManager().findFragmentByTag("success");
							mDrawSuccess.chooseRequestType(FragmentDrawSuccess.BEGIN);
						}
					}
				} catch (Exception e) {
					Toast("理论开始考试，推送有误");
					break;
				}
				break;
			case Constant.ACTION_THEORY_EXAM_END:// 理论考试PC端学生提交成绩
				boolean isEndByPc = MainActivity.this.getFragmentManager()
						.findFragmentById(R.id.fragment_draw) instanceof FragmentDrawSuccess;
				EndExamBean endBean = null;
				String exam_screening_id_end = Utils.getSharedPrefrences(MainActivity.this, "exam_screening_id");
				String student_id_end = Utils.getSharedPrefrences(MainActivity.this, "student_id");

				try {
					endBean = new Gson().fromJson(DataMessage, EndExamBean.class);
					if (isEndByPc && exam_type.equals(Constant.THEORY_STATION) && exam_screening_id_end != null
							&& student_id_end != null) {
						if (endBean.getData().getStudent_id().endsWith(student_id_end)
								&& endBean.getData().getExam_screening_id().equals(exam_screening_id_end)) {
							if (mDrawReady != null) {
								mFgManager.beginTransaction().replace(R.id.fragment_draw, mDrawReady, "wait").commit();
							} else {
								mDrawReady = new FragmentReady();
								mFgManager.beginTransaction().replace(R.id.fragment_draw, mDrawReady, "wait").commit();
							}
						}
					}
				} catch (Exception e) {
					Toast("理论开始考试，推送有误");
					break;

				}
			}
		}
	}

	/**
	 * 发送身份证号
	 * @param idCard 身份证号
	 * @param room_id 房间id
	 * @param teacher_id 老师id
	 */
	public void sendDrawRequest(String idCard, String room_id, String teacher_id,int flag) {
		String drawUrl = BaseActivity.mSUrl + Constant.DRAW + "?id_card=" + idCard + "&room_id=" + room_id + "&teacher_id=" + teacher_id+"&flag="+flag;
		Log.e(">>SendDrawRequest Url<<", drawUrl);
		try {
			GsonRequest<DrawInfor> drawRequest = new GsonRequest<DrawInfor>(Method.GET, drawUrl, DrawInfor.class, null,
					null, new Listener<DrawInfor>() {

				@Override
				public void onResponse(DrawInfor infor) {

					String tips = infor.getMessage();// 抽签提示
					Bundle defeatData = new Bundle();
					defeatData.putString("TipsData", tips);
					Log.i(TAG,tips+"=============================================================");

					int code = infor.getCode();
					if(code==7200){
						Toast(infor.getMessage());
					}else if(code==3100){
						Toast(infor.getMessage());
					}
//					if (mDrawTips != null) {
//						mDrawTips.setTips(tips);
//						mFgManager.beginTransaction().replace(R.id.fragment_draw, mDrawTips, "tips").commit();
//
//					} else {
//						mDrawTips = new FragmentDrawTips();
//						mDrawTips.setArguments(defeatData);
//						mFgManager.beginTransaction().replace(R.id.fragment_draw, mDrawTips, "tips").commit();
//					}
					// 完成抽签请求后，nfc-reader继续循环
					Log.e("DSX", "抽签界面刷新完成，nfc-reader继续循环");
				}
			}, errorListener());
			executeRequest(drawRequest);
		} catch (Exception e) {
			Toast("抽签请求失败");
		}
	}

	public void openProgressDialog() {

		if (dialog == null) {

			dialog = new LoadingDialog(this);
		}
		if (!dialog.isShowing())
			dialog.show();
	}

	public void closeProgressDialog() {

		if (dialog != null) {

			dialog.cancel();
		}
	}
}
