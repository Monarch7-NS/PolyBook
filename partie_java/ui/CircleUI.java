package partie_java.ui;

import partie_java.dao.CircleDAO;
import partie_java.models.Circle;

import javax.swing.*;
import java.awt.*;
import java.util.List;

public class CircleUI extends JFrame {
    public CircleUI(int userId) {
        setTitle("Mes cercles d'amis");
        setSize(400, 300);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        setLocationRelativeTo(null);

        JTextArea area = new JTextArea();
        area.setEditable(false);
        add(new JScrollPane(area), BorderLayout.CENTER);

        CircleDAO dao = new CircleDAO();
        List<Circle> circles = dao.getCirclesForUser(userId);

        for (Circle c : circles) {
            area.append("👥 Cercle : " + c.getName() + "\n");
        }
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new CircleUI(1).setVisible(true));
    }
}
