#include "dropShadow.h"
#include <QDebug>

DropShadowWidget::DropShadowWidget(QWidget *parent)
    : QDialog(parent)
{
    setWindowFlags(Qt::FramelessWindowHint | Qt::Dialog);
  //  setAttribute(Qt::WA_TranslucentBackground);

    //初始化为未按下鼠标左键
    mouse_press = false;
    this->setMouseTracking(true);
}

DropShadowWidget::~DropShadowWidget()
{

}

