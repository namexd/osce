package com.mx.test.bean;

import java.util.Locale;

import com.acs.audiojack.AudioJackReader;
import com.acs.audiojack.Result;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.media.AudioManager;

public class ACR35Reader {
	public static String TAG = "ACR35Reader";
	// default keys
	public static final String DEFAULT_MASTER_KEY_STRING = "00 00 00 00 00 00 00 00 00 00 00 00 00 00 00 00";
	public static final String DEFAULT_AES_KEY_STRING = "4E 61 74 68 61 6E 2E 4C 69 20 54 65 64 64 79 20";
	public static final String DEFAULT_IKSN_STRING = "FF FF 98 76 54 32 10 E0 00 00";
	public static final String DEFAULT_IPEK_STRING = "6A C2 92 FA A1 31 5B 4D 85 8A B3 A3 D7 D5 93 3A";
	// reader objects
	private Context mContext;

	private AudioManager mAudioManager;

	private AudioJackReader mReader;

	private Object mResponseEvent = new Object(); // synchronized lock

	private int mPiccTimeout = 3;// 超时三秒

	private int mPiccCardType;// 卡的类型

	private boolean mResultReady;

	private Result mResult;

	private boolean mPiccAtrReady;

	private byte[] mPiccAtr;

	private boolean mPiccResponseApduReady;

	private byte[] mPiccCommandApdu;

	public byte[] mPiccResponseApdu;

	public boolean isResetSuccess;// ACR35Reader默认模式是睡眠模式，重置是会退出睡眠模式

	/**
	 * get initial reader
	 * 
	 * @param context
	 */
	public void initReader(Context context) {
		mContext = context;
		mAudioManager = (AudioManager) mContext.getSystemService(Context.AUDIO_SERVICE);
		mReader = new AudioJackReader(mAudioManager);
		// 取得UID的操作符
		mPiccCommandApdu = toByteArray("FF CA 00 00 00");
		// 针对的卡的类型
		byte[] cardType = new byte[1];
		toByteArray("8F", cardType);
		mPiccCardType = cardType[0] & 0xFF;

		/* Set the result callback. */
		mReader.setOnResultAvailableListener(new OnResultAvailableListener());
		/* Set the PICC ATR callback. */
		mReader.setOnPiccAtrAvailableListener(new OnPiccAtrAvailableListener());
		/* Set the PICC response APDU callback. */
		mReader.setOnPiccResponseApduAvailableListener(new OnPiccResponseApduAvailableListener());

	}

	public void start() {
		mReader.start();
	}

	public void stop() {
		mReader.stop();
	}

	public void reset() {
		isResetSuccess = false;
		mReader.reset(new OnResetCompleteListener());
	}

	// the result of get the PICC ATR
	private boolean getPiccAtr() {
		boolean ret = false;
		synchronized (mResponseEvent) {
			/* Wait for the PICC ATR. */ // 有问题！！
			while (!mPiccAtrReady && !mResultReady) {
				try {
					mResponseEvent.wait(10000);
				} catch (InterruptedException e) {
				}
				break;
			}
			ret = mPiccAtrReady;
			if (mPiccAtrReady) {
				// 已获得PICC值
				// mPiccAtrPreference.setSummary(toHexString(mPiccAtr));

			} else if (mResultReady) {
				// 已获得结果值 Toast.makeText(mContext,
				// toErrorCodeString(mResult.getErrorCode()),
				// Toast.LENGTH_LONG).show();
			} else {
				// 超时 Toast.makeText(mContext, "The operation timed out.",
				// Toast.LENGTH_LONG).show();
			}
			mPiccAtrReady = false;
			mResultReady = false;
		}
		return ret;
	}

	// the result of get PICC card UID
	private boolean getPiccResponseApdu() {
		boolean ret = false;
		synchronized (mResponseEvent) {
			/* Wait for the PICC response APDU. */
			while (!mPiccResponseApduReady && !mResultReady) {
				try {
					mResponseEvent.wait(10000);
				} catch (InterruptedException e) {
				}
				break;
			}
			ret = mPiccResponseApduReady;
			if (mPiccResponseApduReady) {
				// 已获得PICC值
				// mPiccResponseApduPreference.setSummary(toHexString(mPiccResponseApdu));

			} else if (mResultReady) {
				// 已获得结果值 Toast.makeText(mContext,
				// toErrorCodeString(mResult.getErrorCode()),
				// Toast.LENGTH_LONG).show();
			} else {
				// 超时 Toast.makeText(mContext, "The operation timed out.",
				// Toast.LENGTH_LONG).show();
			}
			mPiccResponseApduReady = false;
			mResultReady = false;
		}
		return ret;
	}

	// judge about the result is ready.
	private boolean getResult() {
		boolean ret = false;
		synchronized (mResponseEvent) {
			/* Wait for the result. */
			while (!mResultReady) {
				try {
					mResponseEvent.wait(10000);
				} catch (InterruptedException e) {
				}
				break;
			}
			ret = mResultReady;
			if (mResultReady) {
				// Show the result. Toast.makeText(mContext,
				// toErrorCodeString(mResult.getErrorCode()),
				// Toast.LENGTH_LONG).show();

			} else {
				// Show the timeout. Toast.makeText(mContext, "The operation
				// timed out.", Toast.LENGTH_LONG).show();

			}
			mResultReady = false;
		}

		return ret;
	}

	// public method
	/**
	 * Converts the byte array to HEX string.
	 * 
	 * @param buffer
	 *            the buffer.
	 * @return the HEX string.
	 */
	public String toHexString(byte[] buffer) {

		String bufferString = "";

		if (buffer != null) {

			for (int i = 0; i < buffer.length; i++) {

				String hexChar = Integer.toHexString(buffer[i] & 0xFF);
				if (hexChar.length() == 1) {
					hexChar = "0" + hexChar;
				}

				bufferString += hexChar.toUpperCase(Locale.US) + " ";
			}
		}

		return bufferString;
	}

	public boolean powerOff() {
		mResultReady = false;
		if (!mReader.piccPowerOff()) {
			/* Show the request queue error. */
			return false;
		} else {
			/* Show the result. */
			return getResult();
			// power off success
		}
	}

	public boolean powerOn() {
		mPiccAtrReady = false;
		mResultReady = false;
		if (!mReader.piccPowerOn(mPiccTimeout, mPiccCardType)) {
			/* Show the request queue error. */
			return false;
		} else {
			/* get the PICC ATR is success or fail */
			return getPiccAtr();
		}
	}

	public boolean transmitAPDU() {
		/* Transmit the command APDU. */
		mPiccResponseApduReady = false;
		mResultReady = false;
		if (!mReader.piccTransmit(mPiccTimeout, mPiccCommandApdu)) {
			/* Show the request queue error. */
			return false;
		} else {

			/* Show the PICC response APDU. */
			return getPiccResponseApdu();
		}
		/* Hide the progress. */
	}

	// private method
	/**
	 * Converts the integer to HEX string.
	 * 
	 * @param i
	 *            the integer.
	 * @return the HEX string.
	 */
	private String toHexString(int i) {

		String hexString = Integer.toHexString(i);

		if (hexString.length() % 2 == 1) {
			hexString = "0" + hexString;
		}

		return hexString.toUpperCase(Locale.US);
	}

	/**
	 * Converts the HEX string to byte array.
	 * 
	 * @param hexString
	 *            the HEX string.
	 * @return the number of bytes.
	 */
	private int toByteArray(String hexString, byte[] byteArray) {

		char c = 0;
		boolean first = true;
		int length = 0;
		int value = 0;
		int i = 0;

		for (i = 0; i < hexString.length(); i++) {

			c = hexString.charAt(i);
			if ((c >= '0') && (c <= '9')) {
				value = c - '0';
			} else if ((c >= 'A') && (c <= 'F')) {
				value = c - 'A' + 10;
			} else if ((c >= 'a') && (c <= 'f')) {
				value = c - 'a' + 10;
			} else {
				value = -1;
			}

			if (value >= 0) {

				if (first) {

					byteArray[length] = (byte) (value << 4);

				} else {

					byteArray[length] |= value;
					length++;
				}

				first = !first;
			}

			if (length >= byteArray.length) {
				break;
			}
		}

		return length;
	}

	/**
	 * Converts the HEX string to byte array.
	 * 
	 * @param hexString
	 *            the HEX string.
	 * @return the byte array.
	 */
	private byte[] toByteArray(String hexString) {

		byte[] byteArray = null;
		int count = 0;
		char c = 0;
		int i = 0;

		boolean first = true;
		int length = 0;
		int value = 0;

		// Count number of hex characters
		for (i = 0; i < hexString.length(); i++) {

			c = hexString.charAt(i);
			if (c >= '0' && c <= '9' || c >= 'A' && c <= 'F' || c >= 'a' && c <= 'f') {
				count++;
			}
		}

		byteArray = new byte[(count + 1) / 2];
		for (i = 0; i < hexString.length(); i++) {

			c = hexString.charAt(i);
			if (c >= '0' && c <= '9') {
				value = c - '0';
			} else if (c >= 'A' && c <= 'F') {
				value = c - 'A' + 10;
			} else if (c >= 'a' && c <= 'f') {
				value = c - 'a' + 10;
			} else {
				value = -1;
			}

			if (value >= 0) {

				if (first) {

					byteArray[length] = (byte) (value << 4);

				} else {

					byteArray[length] |= value;
					length++;
				}

				first = !first;
			}
		}

		return byteArray;
	}

	/**
	 * Checks the reset volume.
	 * 
	 * @return true if current volume is equal to maximum volume.
	 */
	private boolean checkResetVolume() {
		boolean ret = true;

		int currentVolume = mAudioManager.getStreamVolume(AudioManager.STREAM_MUSIC);

		int maxVolume = mAudioManager.getStreamMaxVolume(AudioManager.STREAM_MUSIC);

		if (currentVolume < maxVolume) {

			// showMessageDialog(R.string.info,
			// R.string.message_reset_info_volume);
			ret = false;
		}

		return ret;
	}

	/**
	 * Shows the message dialog.
	 * 
	 * @param titleId
	 *            the title ID.
	 * @param messageId
	 *            the message ID.
	 */
	private void showMessageDialog(int titleId, int messageId) {

		AlertDialog.Builder builder = new AlertDialog.Builder(mContext);

		builder.setMessage(messageId).setTitle(titleId).setPositiveButton("确定", new DialogInterface.OnClickListener() {

			@Override
			public void onClick(DialogInterface dialog, int which) {
				dialog.dismiss();
			}
		});

		builder.show();
	}

	// inside class
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

	private class OnResetCompleteListener implements AudioJackReader.OnResetCompleteListener {
		@Override
		public void onResetComplete(AudioJackReader reader) {
			/* get the complete status */
			isResetSuccess = true;
		}
	}

}
