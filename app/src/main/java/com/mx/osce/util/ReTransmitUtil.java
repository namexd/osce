package com.mx.osce.util;

import android.content.Context;

public class ReTransmitUtil {
	private static final String RE_TRANSMIT_KEY = "ReTransmit";

	public static String[] getReTransmitNames(Context context) {
		String[] Arr = null;
		String str = Utils.getSharedPrefrences(context, RE_TRANSMIT_KEY);
		if (str != null && !str.equals("")) {
			str.trim();
			int Num = 1;
			for (int i = 0; i < str.length(); i++) {
				if (str.charAt(i) == ',') {
					Num++;
				}
			}

			if (Num > 1) {
				Arr = new String[Num];
				for (int i = 0; i < Num; i++) {
					int index = str.indexOf(',');
					if (i != Num - 1) {
						Arr[i] = str.substring(0, index);
						str = str.substring(index + 1, str.length());
					} else {
						Arr[i] = str;
					}
				}
			} else {
				Arr = new String[Num];
				Arr[Num - 1] = str;
			}
		}

		return Arr;
	}

	public static void addReTransmitNames(Context context, String name) {
		String str = Utils.getSharedPrefrences(context, RE_TRANSMIT_KEY);
		//
		if (str == null) {
			str = "";
		}
		if (str.contains(name)) {
			return;
		}

		if (str.equals(""))
			str = name;
		else
			str = str + "," + name;

		Utils.saveSharedPrefrences(context, RE_TRANSMIT_KEY, str);
	}

	public static boolean removeReTransmitNames(Context context, String name) {
		String[] Arr = getReTransmitNames(context);

		if (Arr == null)
			return false;

		for (int i = 0; i < Arr.length; i++) {
			if (Arr[i].equals(name)) {
				String strDst = "";
				for (int j = 0; j < Arr.length; j++) {
					if (j != i) {
						if (j != Arr.length - 1)
							strDst = strDst + Arr[j] + ",";
						else
							strDst = strDst + Arr[j];
					}
				}
				Utils.saveSharedPrefrences(context, RE_TRANSMIT_KEY, strDst);
				break;
			}
		}

		return true;
	}
}
