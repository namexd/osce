package com.mx.bluetooth.bean;

import java.util.ArrayList;

public class Item {

	public static final int ITEM_NORMAL = 0;
	public static final int SECTION = 1;
	public static final int ITEM_SPECIAL = 2;

	public final int type;
	public final String text;
	public final String answer;

	public int sectionPosition;
	public int listPosition;
	public int positionOfParent;
	public String id;

	public ArrayList<PointTermBean> pointTermBeans;
	public PointTermBean bean;

	public Item(int type, String text, String answer) {
		this.type = type;
		this.text = text;
		this.answer = answer;
	}

	@Override
	public String toString() {
		return text;
	}

}
