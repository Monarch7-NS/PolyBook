package partie_java.ui;

import partie_java.dao.FavoriteBookDAO;
import partie_java.models.FavoriteBook;

import javax.swing.*;
import java.awt.*;
import java.util.List;

public class FavoriteUI extends JFrame {
    public FavoriteUI(int userId) {
        setTitle("Mes livres favoris");
        setSize(400, 300);
        setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        setLocationRelativeTo(null);

        JTextArea area = new JTextArea();
        area.setEditable(false);
        add(new JScrollPane(area), BorderLayout.CENTER);

        FavoriteBookDAO dao = new FavoriteBookDAO();
        List<FavoriteBook> favorites = dao.getFavoritesByUser(userId);

        for (FavoriteBook f : favorites) {
            area.append("⭐ Livre ID : " + f.getBookId() + "\n");
        }
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new FavoriteUI(1).setVisible(true));
    }
}
