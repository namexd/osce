#ifndef LABEL
#define LABEL


#include <QWidget>
#include <QLabel>
#include <QMouseEvent>

class Label : public QLabel
{
    Q_OBJECT
public:
    Label(QWidget * parent = 0);
    ~Label(void);

protected:
    void enterEvent(QEvent *);
    void mousePressEvent(QMouseEvent *);
    void mouseReleaseEvent(QMouseEvent * ev);
    void leaveEvent(QEvent *);

signals:
    void clicked();
    void hover();
    void press();
    void leave();
    void move();
};

#endif // LABEL

