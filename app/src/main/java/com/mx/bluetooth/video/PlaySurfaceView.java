package com.mx.bluetooth.video;

import org.MediaPlayer.PlayM4.Player;

import com.hikvision.netsdk.HCNetSDK;
import com.hikvision.netsdk.NET_DVR_PREVIEWINFO;
import com.hikvision.netsdk.RealPlayCallBack;
import com.mx.bluetooth.VideoActivity;

import android.content.Context;
import android.graphics.PixelFormat;
import android.util.Log;
import android.view.Surface;
import android.view.SurfaceHolder;
import android.view.SurfaceView;
import android.view.SurfaceHolder.Callback;

public class PlaySurfaceView extends SurfaceView implements Callback {

	private final String TAG = "PlaySurfaceView";
	private int m_iWidth = 0;
	private int m_iHeight = 0;
	public int m_iPreviewHandle = -1;
	private int m_iPort = -1;
	private boolean m_bSurfaceCreated = false;

	public PlaySurfaceView(VideoActivity video) {
		super((Context) video);
		getHolder().addCallback(this);
	}

	@Override
	public void surfaceChanged(SurfaceHolder arg0, int arg1, int arg2, int arg3) {
		System.out.println("surfaceChanged");
	}

	@Override
	public void surfaceCreated(SurfaceHolder arg0) {
		m_bSurfaceCreated = true;
		setZOrderOnTop(true);
		getHolder().setFormat(PixelFormat.TRANSLUCENT);
		if (-1 == m_iPort) {
			return;
		}
		Surface surface = arg0.getSurface();
		if (true == surface.isValid()) {
			if (false == Player.getInstance().setVideoWindow(m_iPort, 0, arg0)) {
				Log.e(TAG, "Player setVideoWindow failed!");
			}
		}
	}

	@Override
	public void surfaceDestroyed(SurfaceHolder arg0) {
		// TODO Auto-generated method stub
		m_bSurfaceCreated = false;
		if (-1 == m_iPort) {
			return;
		}
		if (true == arg0.getSurface().isValid()) {
			if (false == Player.getInstance().setVideoWindow(m_iPort, 0, null)) {
				Log.e(TAG, "Player setVideoWindow failed!");
			}
		}
	}

	protected void onMeasure(int widthMeasureSpec, int heightMeasureSpec) {
		super.setMeasuredDimension(m_iWidth - 1, m_iHeight - 1);
	}

	public void setParam(int nScreenSize) {
		m_iWidth = nScreenSize / 2;
		m_iHeight = (m_iWidth * 3) / 4;
	}

	public int getCurWidth() {
		return m_iWidth;
	}

	public int getCurHeight() {
		return m_iHeight;
	}

	public void startPreview(int iUserID, int iChan) {
		RealPlayCallBack fRealDataCallBack = getRealPlayerCbf();
		if (fRealDataCallBack == null) {
			Log.e(TAG, "fRealDataCallBack object is failed!");
			return;
		}
		Log.i(TAG, "preview channel:" + iChan);

		NET_DVR_PREVIEWINFO previewInfo = new NET_DVR_PREVIEWINFO();
		previewInfo.lChannel = iChan;
		previewInfo.dwStreamType = 1; // substream
		previewInfo.bBlocked = 1;
		// HCNetSDK start preview
		m_iPreviewHandle = HCNetSDK.getInstance().NET_DVR_RealPlay_V40(iUserID, previewInfo, fRealDataCallBack);
		if (m_iPreviewHandle < 0) {
			Log.e(TAG, "NET_DVR_RealPlay is failed!Err:" + HCNetSDK.getInstance().NET_DVR_GetLastError());
		}
	}

	public void stopPreview() {
		stopPlayer();
		if (!HCNetSDK.getInstance().NET_DVR_StopRealPlay(m_iPreviewHandle)) {
			Log.e(TAG, "NET_DVR_RealPlay is failed!Err:" + HCNetSDK.getInstance().NET_DVR_GetLastError());
		}

	}

	private void stopPlayer() {
		if (m_iPreviewHandle < 0) {
			Log.d(TAG, "已经停止？");
			return;
		}
		// 停止网络播放
		if (HCNetSDK.getInstance().NET_DVR_StopRealPlay(m_iPreviewHandle)) {
			Log.i(TAG, HCNetSDK.getInstance().NET_DVR_StopRealPlay(m_iPreviewHandle) + "");
			Log.i(TAG, "停止实时播放成功！");
		} else {
			Log.e(TAG, "停止实时播放失败！" + HCNetSDK.getInstance().NET_DVR_GetLastError());
			return;
		}
		// 停止本地播放
		if (Player.getInstance().stop(m_iPort)) {
			Log.i(TAG, "停止本地播放成功！");
		} else {
			Log.e(TAG, "停止本地播放失败！");
			return;
		}
		// 关闭视频流
		if (Player.getInstance().closeStream(m_iPort)) {
			Log.i(TAG, "关闭视频流成功！");
		} else {
			Log.e(TAG, "关闭视频流失败！");
			return;
		}
		// 释放播放端口
		if (Player.getInstance().freePort(m_iPort)) {
			Log.i(TAG, "释放播放端口成功！");
		} else {
			Log.e(TAG, "释放播放端口失败！");
			return;
		}
		// 播放端口复位
		m_iPort = -1;
		// 正在播放标记复位
		m_iPreviewHandle = -1;
		Log.i(TAG, "停止播放成功！");
	}

	private RealPlayCallBack getRealPlayerCbf() {
		RealPlayCallBack cbf = new RealPlayCallBack() {
			public void fRealDataCallBack(int iRealHandle, int iDataType, byte[] pDataBuffer, int iDataSize) {
				processRealData(1, iDataType, pDataBuffer, iDataSize, Player.STREAM_REALTIME);
			}
		};
		return cbf;
	}

	private void processRealData(int iPlayViewNo, int iDataType, byte[] pDataBuffer, int iDataSize, int iStreamMode) {
		// Log.i(TAG, "iPlayViewNo:" + iPlayViewNo + ",iDataType:" + iDataType +
		// ",iDataSize:" + iDataSize);
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
				while (!m_bSurfaceCreated) {
					try {
						Thread.sleep(100);
					} catch (InterruptedException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
					Log.i(TAG, "wait 100 for surface, handle:" + iPlayViewNo);
				}

				if (!Player.getInstance().play(m_iPort, getHolder())) {
					Log.e(TAG, "play failed,error:" + Player.getInstance().getLastError(m_iPort));
					return;
				}
				if (!Player.getInstance().playSound(m_iPort)) {
					Log.e(TAG, "playSound failed with error code:" + Player.getInstance().getLastError(m_iPort));
					return;
				}
			}
		} else {
			if (!Player.getInstance().inputData(m_iPort, pDataBuffer, iDataSize)) {
				Log.e(TAG, "inputData failed with: " + Player.getInstance().getLastError(m_iPort));
			}
		}
	}
}

