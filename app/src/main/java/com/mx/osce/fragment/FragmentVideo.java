package com.mx.osce.fragment;

import java.util.List;

import org.MediaPlayer.PlayM4.Player;

import com.android.volley.Response;
import com.android.volley.Request.Method;
import com.hikvision.netsdk.ExceptionCallBack;
import com.hikvision.netsdk.HCNetSDK;
import com.hikvision.netsdk.NET_DVR_DEVICEINFO_V30;
import com.hikvision.netsdk.NET_DVR_PREVIEWINFO;
import com.hikvision.netsdk.RealPlayCallBack;
import com.mx.osce.GradeActivity;
import com.mx.osce.R;
import com.mx.osce.VideoActivity;
import com.mx.osce.bean.VideoBean;
import com.mx.osce.util.Constant;
import com.mx.osce.util.GsonRequest;
import com.mx.osce.util.Utils;
import com.mx.osce.video.PlaySurfaceView;

import android.app.Fragment;
import android.graphics.PixelFormat;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.KeyEvent;
import android.view.LayoutInflater;
import android.view.Surface;
import android.view.SurfaceHolder;
import android.view.SurfaceView;
import android.view.View;
import android.view.ViewGroup;
import android.view.SurfaceHolder.Callback;
import android.view.View.OnClickListener;
import android.widget.ImageButton;
import android.widget.Toast;

public class FragmentVideo extends Fragment implements Callback {
	private ImageButton btn_close = null;

	private SurfaceView m_osurfaceView = null;

	String mNvrIp = "";
	int mNvrPort = 0;
	String mNvrName = "";
	String mNvrPassword = "";

	private NET_DVR_DEVICEINFO_V30 m_oNetDvrDeviceInfoV30 = null;

	/** 登陆NVR成功返回登陆id */
	private int m_iLogID = -1; // return by NET_DVR_Login_v30

	/** 登陆设备返回直播id */
	private int m_iPlayID = -1; // return by NET_DVR_RealPlay_V30

	/** 登陆设备返回看id */
	private int m_iPlaybackID = -1; // return by NET_DVR_PlayBackByTime

	/** 设备端口 */
	private int m_iPort = -1; // play port

	/** 初始通道 */
	private int m_iStartChan = 0; // start channel no

	private int m_iChanNum = 0; // channel number,多屏直播通道参数

	private static PlaySurfaceView playView;

	private final String TAG = "VideoActivity";

	private boolean m_bTalkOn = false;

	private boolean m_bPTZL = false;

	private boolean m_bMultiPlay = false;// 是否多屏直播

	private boolean m_bNeedDecode = true;// 是否解码

	/** Called when the activity is first created. */
	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

		View view = inflater.inflate(R.layout.activity_video, null);
		findViews(view);
		getNetVideoMessage();
		return view;

	}

	private void getNetVideoMessage() {

		System.out.println("room_id=" + Utils.getSharedPrefrences(getActivity().getApplicationContext(), "room_id"));
		String room_id = Utils.getSharedPrefrences(getActivity().getApplicationContext(), "room_id");
		String exam_id = Utils.getSharedPrefrences(getActivity().getApplicationContext(), "exam_id");
		String teacher_id = Utils.getSharedPrefrences(getActivity().getApplicationContext(), "user_id");
		String url = Constant.GET_VIDEO + "?room_id=" + room_id + "&exam_id=" + exam_id + "&teacher_id=" + teacher_id;
		// String url = Constant.Get_Video +
		// "?room_id=1&exam_id=35&teacher_id=132";

		// String url = Constant.GRADE_OPINT + "?station_id=4";

		Log.i(TAG + "get video  url", url);

		GsonRequest<VideoBean> gradeRequest = new GsonRequest<VideoBean>(Method.GET, url,

		VideoBean.class, null, null, new Response.Listener<VideoBean>() {

			@Override
			public void onResponse(VideoBean infor) {

				try {

					if (infor.getCode() == 1) {
						List<VideoBean> data = infor.getData();
						FragmentVideo.this.mNvrIp = data.get(0).getIp();
						FragmentVideo.this.mNvrPort = data.get(0).getPort();
						FragmentVideo.this.mNvrName = data.get(0).getUsername();
						FragmentVideo.this.mNvrPassword = data.get(0).getPassword();
						if (!initeActivity()) {

							return;
						} else {
							if (autoLoginNvr()) {
								autoPreview();
							}
						}
					} else {

					}

				} catch (Exception e) {
					System.out.println("DDDDD" + e.getMessage());
					Toast.makeText(getActivity(), "获得摄像头失败", Toast.LENGTH_SHORT).show();
				}

			}
		}, ((GradeActivity) getActivity()).errorListener());

		((GradeActivity) getActivity()).executeRequest(gradeRequest);
	}

	private boolean initeActivity() {

		m_osurfaceView.getHolder().addCallback(this);
		return true;
	}

	private void findViews(View view) {

		// m_oParamCfgBtn = (Button) findViewById(R.id.btn_ParamCfg);
		m_osurfaceView = (SurfaceView) view.findViewById(R.id.Sur_Player);
		btn_close = (ImageButton) view.findViewById(R.id.btn_close);
		btn_close.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				stopSinglePlayer();
				Cleanup();
				android.os.Process.killProcess(android.os.Process.myPid());

			}
		});
	}

	@Override
	public void surfaceCreated(SurfaceHolder holder) {
		// TODO Auto-generated method stub
		m_osurfaceView.getHolder().setFormat(PixelFormat.TRANSLUCENT);
		Log.i(TAG, "surface is created" + m_iPort);
		if (-1 == m_iPort) {
			return;
		}
		Surface surface = holder.getSurface();
		if (true == surface.isValid()) {
			if (false == Player.getInstance().setVideoWindow(m_iPort, 0, holder)) {
				Log.e(TAG, "Player setVideoWindow failed!");
			}
		}

		startSinglePreview();
	}

	@Override
	public void surfaceChanged(SurfaceHolder holder, int format, int width, int height) {
		// TODO Auto-generated method stub

	}

	@Override
	public void surfaceDestroyed(SurfaceHolder holder) {
		// TODO Auto-generated method stub
		Log.i(TAG, "Player setVideoWindow release!" + m_iPort);
		if (-1 == m_iPort) {
			return;
		}
		if (true == holder.getSurface().isValid()) {
			if (false == Player.getInstance().setVideoWindow(m_iPort, 0, null)) {
				Log.e(TAG, "Player setVideoWindow failed!");
			}
		}
	}

	private void startSinglePreview() {
		if (m_iPlaybackID >= 0) {
			Log.i(TAG, "Please stop palyback first");
			return;
		}
		RealPlayCallBack fRealDataCallBack = getRealPlayerCbf();
		if (fRealDataCallBack == null) {
			Log.e(TAG, "fRealDataCallBack object is failed!");
			return;
		}
		Log.i(TAG, "m_iStartChan:" + m_iStartChan);

		NET_DVR_PREVIEWINFO previewInfo = new NET_DVR_PREVIEWINFO();
		previewInfo.lChannel = m_iStartChan;
		previewInfo.dwStreamType = 1; // substream
		previewInfo.bBlocked = 1;
		// HCNetSDK start preview
		m_iPlayID = HCNetSDK.getInstance().NET_DVR_RealPlay_V40(m_iLogID, previewInfo, fRealDataCallBack);
		if (m_iPlayID < 0) {
			Log.e(TAG, "NET_DVR_RealPlay is failed!Err:" + HCNetSDK.getInstance().NET_DVR_GetLastError());
			return;
		}

		Log.i(TAG, "NetSdk Play sucess ***********************3***************************");
		// m_oPreviewBtn.setText("Stop");
	}

	/**
	 * @fn getRealPlayerCbf
	 * @author zhuzhenlei
	 * @brief get realplay callback instance
	 * @param NULL
	 *            [in]
	 * @param NULL
	 *            [out]
	 * @return callback instance
	 */
	private RealPlayCallBack getRealPlayerCbf() {
		RealPlayCallBack cbf = new RealPlayCallBack() {
			public void fRealDataCallBack(int iRealHandle, int iDataType, byte[] pDataBuffer, int iDataSize) {
				// player channel 1
				processRealData(1, iDataType, pDataBuffer, iDataSize, Player.STREAM_REALTIME);
			}
		};
		return cbf;
	}

	/**
	 * @fn processRealData
	 * @author zhuzhenlei
	 * @brief process real data
	 * @param iPlayViewNo
	 *            - player channel [in]
	 * @param iDataType
	 *            - data type [in]
	 * @param pDataBuffer
	 *            - data buffer [in]
	 * @param iDataSize
	 *            - data size [in]
	 * @param iStreamMode
	 *            - stream mode [in]
	 * @param NULL
	 *            [out]
	 * @return NULL
	 */
	public void processRealData(int iPlayViewNo, int iDataType, byte[] pDataBuffer, int iDataSize, int iStreamMode) {
		if (!m_bNeedDecode) {
			// Log.i(TAG, "iPlayViewNo:" + iPlayViewNo + ",iDataType:" +
			// iDataType + ",iDataSize:" + iDataSize);
		} else {
			if (HCNetSDK.NET_DVR_SYSHEAD == iDataType) {
				if (m_iPort >= 0) {
					return;
				}
				m_iPort = Player.getInstance().getPort();
				if (m_iPort == -1) {
					Log.e(TAG, "getPort is failed with: " + Player.getInstance().getLastError(m_iPort));
					return;
				}
				Log.i(TAG, "getPort succ with: " + m_iPort);
				if (iDataSize > 0) {
					if (!Player.getInstance().setStreamOpenMode(m_iPort, iStreamMode)) // set
																						// stream
																						// mode
					{
						Log.e(TAG, "setStreamOpenMode failed");
						return;
					}
					if (!Player.getInstance().openStream(m_iPort, pDataBuffer, iDataSize, 2 * 1024 * 1024)) // open
																											// stream
					{
						Log.e(TAG, "openStream failed");
						return;
					}
					if (!Player.getInstance().play(m_iPort, m_osurfaceView.getHolder())) {
						Log.e(TAG, "play failed");
						return;
					}
					if (!Player.getInstance().playSound(m_iPort)) {
						Log.e(TAG, "playSound failed with error code:" + Player.getInstance().getLastError(m_iPort));
						return;
					}
				}
			} else {
				if (!Player.getInstance().inputData(m_iPort, pDataBuffer, iDataSize)) {
					// Log.e(TAG, "inputData failed with: " +
					// Player.getInstance().getLastError(m_iPort));
					for (int i = 0; i < 4000 && m_iPlaybackID >= 0; i++) {
						if (!Player.getInstance().inputData(m_iPort, pDataBuffer, iDataSize))
							Log.e(TAG, "inputData failed with: " + Player.getInstance().getLastError(m_iPort));
						else
							break;
						try {
							Thread.sleep(10);
						} catch (InterruptedException e) {
							// TODO Auto-generated catch block
							e.printStackTrace();

						}
					}
				}

			}
		}

	}

	public void Cleanup() {
		// release player resource

		Player.getInstance().freePort(m_iPort);
		m_iPort = -1;

		// release net SDK resource
		HCNetSDK.getInstance().NET_DVR_Cleanup();
	}

	public boolean onKeyDown(int keyCode, KeyEvent event) {

		switch (keyCode) {
		case KeyEvent.KEYCODE_BACK:

			stopSinglePlayer();
			Cleanup();
			android.os.Process.killProcess(android.os.Process.myPid());
			break;
		default:
			break;
		}
		return true;
	}

	/** 自动登陆NVR */
	private boolean autoLoginNvr() {
		try {
			if (m_iLogID < 0) {
				// login on the device
				m_iLogID = loginDevice();
				if (m_iLogID < 0) {
					Log.e(TAG, "This device logins failed!");
					return false;
				}
				// get instance of exception callback and set
				ExceptionCallBack oexceptionCbf = getExceptiongCbf();
				if (oexceptionCbf == null) {
					Log.e(TAG, "ExceptionCallBack object is failed!");
					return false;
				}

				if (!HCNetSDK.getInstance().NET_DVR_SetExceptionCallBack(oexceptionCbf)) {
					Log.e(TAG, "NET_DVR_SetExceptionCallBack is failed!");
					return false;
				}
				Log.i(TAG, "Login sucess ****************************1***************************");
				return true;

			} else {
				// whether we have logout
				if (!HCNetSDK.getInstance().NET_DVR_Logout_V30(m_iLogID)) {
					Log.e(TAG, " NET_DVR_Logout is failed!");
					return false;
				}
				m_iLogID = -1;
			}
		} catch (Exception err) {
			Log.e(TAG, "error: " + err.toString());
		}
		return false;
	}

	/**
	 * @fn getExceptiongCbf
	 * @author zhuzhenlei
	 * @brief process exception
	 * @param NULL
	 *            [in]
	 * @param NULL
	 *            [out]
	 * @return exception instance
	 */
	private ExceptionCallBack getExceptiongCbf() {
		ExceptionCallBack oExceptionCbf = new ExceptionCallBack() {
			public void fExceptionCallBack(int iType, int iUserID, int iHandle) {
				System.out.println("recv exception, type:" + iType);
			}
		};
		return oExceptionCbf;
	}

	/**
	 * @fn loginDevice
	 * @author zhuzhenlei
	 * @brief login on device
	 * @param NULL
	 *            [in]
	 * @param NULL
	 *            [out]
	 * @return login ID
	 */
	private int loginDevice() {
		// get instance
		m_oNetDvrDeviceInfoV30 = new NET_DVR_DEVICEINFO_V30();
		if (null == m_oNetDvrDeviceInfoV30) {
			Log.e(TAG, "HKNetDvrDeviceInfoV30 new is failed!");
			return -1;
		}
		// String strIP = m_oIPAddr.getText().toString();
		String strIP = mNvrIp;
		// int nPort = Integer.parseInt(m_oPort.getText().toString());
		int nPort = mNvrPort;
		// String strUser = m_oUser.getText().toString();
		String strUser = mNvrName;
		// String strPsd = m_oPsd.getText().toString();
		String strPsd = mNvrPassword;
		// call NET_DVR_Login_v30 to login on, port 8000 as default
		int iLogID = HCNetSDK.getInstance().NET_DVR_Login_V30(strIP, nPort, strUser, strPsd, m_oNetDvrDeviceInfoV30);
		if (iLogID < 0) {
			Log.e(TAG, "NET_DVR_Login is failed!Err:" + HCNetSDK.getInstance().NET_DVR_GetLastError());
			return -1;
		}
		if (m_oNetDvrDeviceInfoV30.byChanNum > 0) {
			m_iStartChan = m_oNetDvrDeviceInfoV30.byStartChan;
			m_iChanNum = m_oNetDvrDeviceInfoV30.byChanNum;
		} else if (m_oNetDvrDeviceInfoV30.byIPChanNum > 0) {
			m_iStartChan = m_oNetDvrDeviceInfoV30.byStartDChan;
			m_iChanNum = m_oNetDvrDeviceInfoV30.byIPChanNum + m_oNetDvrDeviceInfoV30.byHighDChanNum * 256;
		}
		Log.i(TAG, "NET_DVR_Login is Successful!");

		return iLogID;
	}

	private void autoPreview() {
		try {

			if (m_iLogID < 0) {
				Log.e(TAG, "please login on device first");
				return;
			}
			if (m_bNeedDecode) {
				if (m_iPlayID < 0) {
					startSinglePreview();
				} else {
					stopSinglePreview();
					// m_oPreviewBtn.setText("Preview");
					m_iPlayID = -1;
				}
			}
		} catch (Exception err) {
			Log.e(TAG, "error: " + err.toString());
		}
	}

	/**
	 * @fn stopSinglePreview
	 * @author zhuzhenlei
	 * @brief stop preview
	 * @param NULL
	 *            [in]
	 * @param NULL
	 *            [out]
	 * @return NULL
	 */
	private void stopSinglePreview() {
		if (m_iPlayID < 0) {
			Log.e(TAG, "m_iPlayID < 0");
			return;
		}

		// net sdk stop preview
		if (!HCNetSDK.getInstance().NET_DVR_StopRealPlay(m_iPlayID)) {
			Log.e(TAG, "StopRealPlay is failed!Err:" + HCNetSDK.getInstance().NET_DVR_GetLastError());
			return;
		}
		m_iPlayID = -1;
		stopSinglePlayer();
	}

	private void stopSinglePlayer() {

		Player.getInstance().stopSound();
		// player stop play
		if (!Player.getInstance().stop(m_iPort)) {
			Log.e(TAG, "stop is failed!");
			return;
		}

		if (!Player.getInstance().closeStream(m_iPort)) {
			Log.e(TAG, "closeStream is failed!");
			return;
		}
		if (!Player.getInstance().freePort(m_iPort)) {
			Log.e(TAG, "freePort is failed!" + m_iPort);
			return;
		}
		m_iPort = -1;
		// m_iPlayID = -1;
	}

}
