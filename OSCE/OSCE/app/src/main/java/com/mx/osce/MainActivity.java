package com.mx.osce;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import javax.xml.transform.ErrorListener;
import javax.xml.transform.TransformerException;

import com.acs.audiojack.AudioJackReader;
import com.acs.audiojack.AudioJackReader.OnResetCompleteListener;
import com.acs.audiojack.DukptReceiver;
import com.acs.audiojack.Result;
import com.android.volley.Request.Method;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.Response.Listener;
import com.google.gson.Gson;
import com.mx.osce.adapter.GridViewAdapter;
import com.mx.osce.bean.CurrentGroupBean;
import com.mx.osce.bean.CurrentGroupStudentBean;
import com.mx.osce.bean.DrawInfor;
import com.mx.osce.bean.EndExamBean;
import com.mx.osce.bean.NextGroupBean;
import com.mx.osce.bean.PollingStudentBean;
import com.mx.osce.bean.PollingStudentInfo;
import com.mx.osce.bean.StartExamBean;
import com.mx.osce.fragment.FragmentDrawSuccess;
import com.mx.osce.fragment.FragmentDrawTips;
import com.mx.osce.fragment.FragmentReady;
import com.mx.osce.fragment.FragmentReady.OnReadyListener;
import com.mx.osce.service.UploadScroeService;
import com.mx.osce.service.UploadScroeService.OnProgressListener;
import com.mx.osce.util.Constant;
import com.mx.osce.util.GsonRequest;
import com.mx.osce.util.Utils;

import android.app.FragmentManager;
import android.content.BroadcastReceiver;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.ServiceConnection;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
import android.media.AudioManager;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.IBinder;
import android.os.Message;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.WindowManager;
import android.widget.GridView;
import android.widget.ImageButton;
import android.widget.TextView;
import android.widget.Toast;

/** 抽签，开始考试 */
public class MainActivity extends BaseActivity {

	/********************************************************** 读卡器默认密码相关 *********************************************************/
	public static final String DEFAULT_MASTER_KEY_STRING = "00 00 00 00 00 00 00 00 00 00 00 00 00 00 00 00";
	public static final String DEFAULT_AES_KEY_STRING = "4E 61 74 68 61 6E 2E 4C 69 20 54 65 64 64 79 20";
	public static final String DEFAULT_IKSN_STRING = "FF FF 98 76 54 32 10 E0 00 00";
	public static final String DEFAULT_IPEK_STRING = "6A C2 92 FA A1 31 5B 4D 85 8A B3 A3 D7 D5 93 3A";

	/********************************************************** 读卡器默认设置相关 *********************************************************/
	private AudioManager mAudioManager;
	private AudioJackReader mReader;
	private DukptReceiver mDukptReceiver = new DukptReceiver();
	private byte[] mPiccResponseApdu;
	private byte[] mPiccAtr;
	private Object mResponseEvent = new Object();
	private Result mResult;
	private AudioHandler mHandler;
	protected boolean mResultReady;
	protected boolean mPiccAtrReady;
	protected int mPiccTimeout;
	protected int mPiccCardType;
	protected byte[] mPiccCommandApdu;
	protected boolean mPiccResponseApduReady;
	private byte[] mNewMasterKey = new byte[16];
	private byte[] mMasterKey = new byte[16];
	private byte[] mAesKey = new byte[16];
	private byte[] mIksn = new byte[10];
	private byte[] mIpek = new byte[16];
	private byte[] mPiccRfConfig = new byte[19];

	/**********************************************************************************************************************************/

	// 当前考站
	private TextView mTextTitleStationName;

	// 当前时间
	private TextView mTextTitleTime;

	private TextView mTextVideo;

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

	private StartReaderTask mReaderTask;

	private ServiceConnection conn;// 异步上传成绩ServiceConnection

	private UploadScroeService msgService;// 异步上传成绩Service

	private Intent intent;// 启动上传成绩Service的intent

	@Override
	protected void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState, this);

		setContentView(R.layout.activity_main);

		this.getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);

		{// 读卡器设置
			mReaderTask = new StartReaderTask();
			mAudioManager = (AudioManager) getSystemService(Context.AUDIO_SERVICE);
			mReader = new AudioJackReader(mAudioManager, true);
			configReader();// 配置读卡器
			readerListener();// 读卡器监听器

			mHandler = new AudioHandler();
		}
		findWidget();
		startUploadScroeService();
		mReaderTask.execute();// 异步线程启动Reader
		// initActionBar();
	}

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

		mReader.stop();// 停止Nfc-Reader
	}

	@Override
	protected void onDestroy() {
		Log.v("MediaPlayerActivity", "MediaPlayerActivity onDestroy");
		unbindService(conn);
		super.onDestroy();
	}

	/** 配置Reader */
	private void configReader() {
		/* Load the new master key. */
		String newMasterKeyString = DEFAULT_MASTER_KEY_STRING;
		Utils.toByteArray(newMasterKeyString, mNewMasterKey);
		newMasterKeyString = Utils.toHexString(mNewMasterKey);

		/* Load the master key. */
		String masterKeyString = DEFAULT_MASTER_KEY_STRING;
		Utils.toByteArray(masterKeyString, mMasterKey);
		masterKeyString = Utils.toHexString(mMasterKey);

		/* Load the AES key. */
		String aesKeyString = DEFAULT_AES_KEY_STRING;
		Utils.toByteArray(aesKeyString, mAesKey);
		aesKeyString = Utils.toHexString(mAesKey);

		/* Load the IKSN. */
		String iksnString = DEFAULT_IKSN_STRING;
		Utils.toByteArray(iksnString, mIksn);

		/* Load the IPEK. */
		String ipekString = DEFAULT_IPEK_STRING;
		Utils.toByteArray(ipekString, mIpek);

		/* Load the PICC timeout. */
		String piccTimeoutString = "2";

		try {
			mPiccTimeout = Integer.parseInt(piccTimeoutString);
		} catch (NumberFormatException e) {
			mPiccTimeout = 1;
		}
		piccTimeoutString = Integer.toString(mPiccTimeout);

		/* Load the PICC card type. */
		String piccCardTypeString = null;

		if ((piccCardTypeString == null) || piccCardTypeString.equals("")) {
			piccCardTypeString = "8F";
		}

		byte[] cardType = new byte[1];
		Utils.toByteArray(piccCardTypeString, cardType);
		mPiccCardType = cardType[0] & 0xFF;
		piccCardTypeString = Utils.toHexString(mPiccCardType);

		/* Load the PICC command APDU. */
		String piccCommandApduString = null;
		if ((piccCommandApduString == null) || (piccCommandApduString.equals(""))) {
			piccCommandApduString = "FF CA 00 00 00";
		}

		mPiccCommandApdu = Utils.toByteArray(piccCommandApduString);
		piccCommandApduString = Utils.toHexString(mPiccCommandApdu);

		/* Load the PICC RF configuration. */
		String piccRfConfigString = null;
		if ((piccRfConfigString == null) || piccRfConfigString.equals("")
				|| (Utils.toByteArray(piccRfConfigString, mPiccRfConfig) != 19)) {

			piccRfConfigString = "07 85 85 85 85 85 85 85 85 69 69 69 69 69 69 69 69 3F 3F";
			Utils.toByteArray(piccRfConfigString, mPiccRfConfig);
		}
		piccRfConfigString = Utils.toHexString(mPiccRfConfig);

	}

	/** 为Reader设置相关监听器 */
	private void readerListener() {
		/* Set the result callback. */
		mReader.setOnResultAvailableListener(new OnResultAvailableListener());

		/* Set the PICC ATR callback. */
		mReader.setOnPiccAtrAvailableListener(new OnPiccAtrAvailableListener());

		/* Set the PICC response APDU callback. */
		mReader.setOnPiccResponseApduAvailableListener(new OnPiccResponseApduAvailableListener());

		/* Set the key serial number. */
		mDukptReceiver.setKeySerialNumber(mIksn);

		/* Load the initial key. */
		mDukptReceiver.loadInitialKey(mIpek);

	}

	/**
	 * 设置最大音量
	 * 
	 * @return true if current volume is equal to maximum volume.
	 */
	private void setMaxVolume() {
		int currentVolume = mAudioManager.getStreamVolume(AudioManager.STREAM_MUSIC);
		final int maxVolume = mAudioManager.getStreamMaxVolume(AudioManager.STREAM_MUSIC);
		if (currentVolume < maxVolume) {
			mAudioManager.setStreamVolume(AudioManager.STREAM_MUSIC, maxVolume, AudioManager.ADJUST_RAISE);
		}
	}

	class AudioHandler extends Handler {

		public static final int RESET = 0;
		public static final int POWER_OFF = 1;
		public static final int POWER_ON = 2;
		public static final int TRANSMIT = 3;
		public static final int ERROR_TIP = 4;
		protected static final int OBAINT_DATA = 5;
		public static final int CURRENT_STUDENT = 100;

		public static final int POLLING_STUDNET = 200;

		@Override
		public void handleMessage(Message msg) {
			super.handleMessage(msg);
			switch (msg.what) {
			case RESET:
				setMaxVolume();
				reset();
				break;
			case POWER_OFF:
				powerOff();
				break;
			case POWER_ON:
				powerOn();
				break;
			case TRANSMIT:
				transmit();
				break;
			case OBAINT_DATA:
				// 拿到腕表的uid,去掉中间的空格
				String watchUid = msg.obj.toString().replace(" ", "");
				if (watchUid.length() == 18 || watchUid.length() == 12) {// TODO 兼顾定制版，非定制版Pad的UID读取
					watchUid = watchUid.substring(0, watchUid.length() - 4);
				}

				// 不是抽签提示碎片，丢弃数据
				if (mFgManager.findFragmentById(R.id.fragment_draw) instanceof FragmentReady
						|| mFgManager.findFragmentById(R.id.fragment_draw) instanceof FragmentDrawSuccess) {

					mHandler.removeMessages(AudioHandler.OBAINT_DATA);
					mHandler.sendEmptyMessage(AudioHandler.POWER_OFF);
				} else {

					sendDrawRequest(watchUid, // 抽签一次
							Utils.getSharedPrefrences(MainActivity.this, "room_id"),
							Utils.getSharedPrefrences(MainActivity.this, "user_id"));
				}

				break;
			case CURRENT_STUDENT:

				if (mDrawSuccess != null) {

					// mDrawSuccess = (FragmentDrawSuccess)
					// manager.findFragmentByTag("success");
					mFgManager.beginTransaction().replace(R.id.fragment_draw, mDrawSuccess, "success").commit();

				} else {
					mDrawSuccess = new FragmentDrawSuccess();
					mFgManager.beginTransaction().replace(R.id.fragment_draw, mDrawSuccess, "success").commit();
				}
				break;
			case POLLING_STUDNET:

				mRefreshStudent.setVisibility(View.VISIBLE);
				break;
			}
		}
	}

	/** Reset */
	public void reset() {
		/* Reset the reader. */
		mReader.reset(new OnResetCompleteListener() {
			@Override
			public void onResetComplete(AudioJackReader arg0) {
				Log.e("DSX", "reset 成功");
				mHandler.sendEmptyMessage(AudioHandler.POWER_ON);
			}
		});
	}

	/** PowerOn */
	public void powerOn() {
		new Thread() {
			@Override
			public void run() {
				Log.e("DSX", "poweron 開始");
				/* Power on the PICC. */
				mPiccAtrReady = false;
				mResultReady = false;
				if (!mReader.piccPowerOn(mPiccTimeout, mPiccCardType)) {
					/* Show the request queue error. */
					Log.e("powerOn", "The request cannot be queued.");

					mHandler.sendEmptyMessage(AudioHandler.POWER_OFF);
				} else {
					/* Show the PICC ATR. */
					synchronized (mResponseEvent) {
						/* Wait for the PICC ATR. */
						while (!mPiccAtrReady && !mResultReady) {
							try {
								mResponseEvent.wait(10000);
							} catch (InterruptedException e) {
							}
							break;
						}
						if (mPiccAtrReady) {
							Log.e("poweron", "success,PiccAtr:" + Utils.toHexString(mPiccAtr));
							Log.e("DSX", "poweron 成功");

							mHandler.sendEmptyMessage(AudioHandler.TRANSMIT);
						} else if (mResultReady) {

							mHandler.sendEmptyMessage(AudioHandler.POWER_OFF);
						} else {
							Log.e("poweron", "The operation timed out.");

							mHandler.sendEmptyMessage(AudioHandler.POWER_OFF);
						}
						mPiccAtrReady = false;
						mResultReady = false;
					}
				}
			}
		}.start();
	}

	/** Transmit */
	public void transmit() {
		new Thread() {
			@Override
			public void run() {
				/* Transmit the command APDU. */
				mPiccResponseApduReady = false;
				mResultReady = false;
				if (!mReader.piccTransmit(mPiccTimeout, mPiccCommandApdu)) {
					/* Show the request queue error. */
					Log.e("transmit", "The request cannot be queued.");

					mHandler.sendEmptyMessage(AudioHandler.POWER_OFF);
				} else {
					/* Show the PICC response APDU. */
					synchronized (mResponseEvent) {
						/* Wait for the PICC response APDU. */
						while (!mPiccResponseApduReady && !mResultReady) {
							try {
								mResponseEvent.wait(10000);
							} catch (InterruptedException e) {
							}
						}
						if (mPiccResponseApduReady) {
							Log.e("transmit", "success,apdu:" + Utils.toHexString(mPiccResponseApdu));
							Log.e("DSX", "transmit 成功");
							Message msg = mHandler.obtainMessage();
							msg.what = AudioHandler.OBAINT_DATA;
							msg.obj = Utils.toHexString(mPiccResponseApdu);
							mHandler.sendMessage(msg);
							Log.e("DSX", "获取NFC-UID 成功");
						} else if (mResultReady) {
							Log.e("DSX", "transmit 失败");

							mHandler.sendEmptyMessage(AudioHandler.POWER_OFF);
						} else {
							Log.e("DSX", "transmit 超时");
							Log.e("transmit", "The operation timed out.");

							mHandler.sendEmptyMessage(AudioHandler.POWER_OFF);
						}
						mPiccResponseApduReady = false;
						mResultReady = false;
					}
				}

			}
		}.start();
	}

	/** PowerOff */
	public void powerOff() {
		new Thread() {
			@Override
			public void run() {
				/* Power off the PICC. */
				mResultReady = false;
				if (!mReader.piccPowerOff()) {
					/* Show the request queue error. */
					Log.e("powerOff", "The request cannot be queued.");
					// Message msg = new Message();

					mHandler.sendEmptyMessage(AudioHandler.RESET);
				} else {
					/* Show the result. */
					synchronized (mResponseEvent) {
						while (!mResultReady) {
							try {
								mResponseEvent.wait(10000);
							} catch (InterruptedException e) {
							}
							break;
						}
						if (mResultReady) {
							Log.e("DSX", "powerOff 成功");
							// Message msg = new Message();
							// msg.what = AudioHandler.POWER_ON;

							mHandler.sendEmptyMessage(AudioHandler.POWER_ON);
						} else {
							Log.e("DSX", "powerOff 超时");
							// Message msg = new Message();
							// msg.what = AudioHandler.RESET;
							mHandler.sendEmptyMessage(AudioHandler.RESET);
						}
						mResultReady = false;
					}
				}
			}
		}.start();
	}

	private final BroadcastReceiver mHeadsetPlugReceiver = new BroadcastReceiver() {

		@Override
		public void onReceive(Context context, Intent intent) {

			if (intent.getAction().equals(Intent.ACTION_HEADSET_PLUG)) {
				boolean plugged = (intent.getIntExtra("state", 0) == 1);
				/* Mute the audio output if the reader is unplugged. */
				mReader.setMute(!plugged);
				if (plugged) {
					Log.e("DSX", "检测是否开启NFC");
					mReader.start();// add
					sendBroadcast(new Intent().setAction(Constant.ACTION_START_NFC_READER));
				} else {
					// mIsNfcReady = false;
					// mReader.getStatus();
					mReader.stop();// add
					mHandler.removeMessages(AudioHandler.RESET);
					mHandler.removeMessages(AudioHandler.POWER_ON);
					mHandler.removeMessages(AudioHandler.POWER_OFF);
					mHandler.removeMessages(AudioHandler.TRANSMIT);
					mHandler.removeMessages(AudioHandler.OBAINT_DATA);
					mHandler.removeMessages(AudioHandler.ERROR_TIP);
				}
			}
		}
	};

	private class OnResultAvailableListener implements AudioJackReader.OnResultAvailableListener {

		@Override
		public void onResultAvailable(AudioJackReader reader, Result result) {

			synchronized (mResponseEvent) {

				/* Store the result. */
				mResult = result;

				/* Trigger the response event. */
				mResultReady = true;
				mResponseEvent.notifyAll();
			}
		}
	}

	private class OnPiccAtrAvailableListener implements AudioJackReader.OnPiccAtrAvailableListener {

		@Override
		public void onPiccAtrAvailable(AudioJackReader reader, byte[] atr) {

			synchronized (mResponseEvent) {

				/* Store the PICC ATR. */
				mPiccAtr = new byte[atr.length];
				System.arraycopy(atr, 0, mPiccAtr, 0, atr.length);

				/* Trigger the response event. */
				mPiccAtrReady = true;
				mResponseEvent.notifyAll();
			}
		}
	}

	private class OnPiccResponseApduAvailableListener implements AudioJackReader.OnPiccResponseApduAvailableListener {

		@Override
		public void onPiccResponseApduAvailable(AudioJackReader reader, byte[] responseApdu) {

			synchronized (mResponseEvent) {

				/* Store the PICC response APDU. */
				mPiccResponseApdu = new byte[responseApdu.length];
				System.arraycopy(responseApdu, 0, mPiccResponseApdu, 0, responseApdu.length);

				/* Trigger the response event. */
				mPiccResponseApduReady = true;
				mResponseEvent.notifyAll();
			}
		}
	}

	@Override
	public void onClick(View v) {
		mDrawReady.setOnReadyListener(new OnReadyListener() {
			@Override
			public void onReady() {
				Log.e("DSX", "NFC Reader 启动");
				mHandler.sendEmptyMessage(AudioHandler.RESET);
			}
		});
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

									mHandler.sendEmptyMessage(AudioHandler.CURRENT_STUDENT);// 切换视图
								}

							} else {

								Toast("请等待,等待码" + currentStudent.getCode());
							}
						}
					}, refreshErrorListener());

			executeRequest(refreshRequest);

		} catch (Exception e) {
			isRefrushStudent = true;
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

	/***
	 * 发送腕表的抽签Get网络请求
	 * 
	 * @param watchUid
	 *            腕表内置NFC芯片的Uid
	 * @param room_id
	 *            考试的房间号
	 */
	public void sendDrawRequest(String watchUid, String room_id, String teacher_id) {

//		Map<String, String> params = new HashMap<String, String>();
//		params.put("uid", watchUid);
//		params.put("room_id", room_id);
//		params.put("teacher_id", teacher_id);
		String drawUrl = BaseActivity.mSUrl + Constant.DRAW + "?uid=" + watchUid + "&room_id=" + room_id + "&teacher_id=" + teacher_id;
		Log.e(">>>SendDrawRequest Url<<<", drawUrl);
		try {
			GsonRequest<DrawInfor> drawRequest = new GsonRequest<DrawInfor>(Method.GET, drawUrl, DrawInfor.class, null,
					null, new Listener<DrawInfor>() {

						@Override
						public void onResponse(DrawInfor infor) {

							String tips = infor.getMessage();// 抽签提示
							Bundle defeatData = new Bundle();
							defeatData.putString("TipsData", tips);

							if (mDrawTips != null) {
								mDrawTips.setTips(tips);
								mFgManager.beginTransaction().replace(R.id.fragment_draw, mDrawTips, "tips").commit();

							} else {
								mDrawTips = new FragmentDrawTips();
								mDrawTips.setArguments(defeatData);
								mFgManager.beginTransaction().replace(R.id.fragment_draw, mDrawTips, "tips").commit();
							}
							// 完成抽签请求后，nfc-reader继续循环
							Log.e("DSX", "抽签界面刷新完成，nfc-reader继续循环");
							mHandler.removeMessages(AudioHandler.OBAINT_DATA);
							mHandler.sendEmptyMessage(AudioHandler.POWER_OFF);
						}
					}, errorListener());
			executeRequest(drawRequest);
		} catch (Exception e) {
			Toast("抽签请求失败");
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
				mHandler.sendEmptyMessage(AudioHandler.RESET);
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
							// if (pollingStudent.getStation_id() != null &&
							// pollingStudent.getStation_id().length() > 0) {
							// Utils.saveSharedPrefrences(MainActivity.this,
							// "student_station_id",
							// pollingStudent.getStation_id());
							// }
							mHandler.sendEmptyMessageDelayed(AudioHandler.CURRENT_STUDENT, 1000);
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

	// 异步执行Reader.start
	private class StartReaderTask extends AsyncTask<Void, Void, Void> {

		@Override
		protected Void doInBackground(Void... params) {
			mReader.start();
			return null;
		}

		@Override
		protected void onPostExecute(Void result) {
			super.onPostExecute(result);
		}
	}
}
