package partie_java.ui;



import java.awt.*;
import javax.swing.*;
import partie_java.models.User;


public class MainAppUI extends JFrame {
    public MainAppUI(User user) {
        setTitle("Bienvenue, " + user.getUsername());
        setSize(800, 600);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);

        JButton livresBtn = new JButton("Voir les livres");
        JButton favorisBtn = new JButton("Favoris");
        JButton listesBtn = new JButton("Mes listes");

        livresBtn.addActionListener(e -> new BookCatalogUI().setVisible(true));
        favorisBtn.addActionListener(e -> new FavoriteUI(user.getId()).setVisible(true));
        listesBtn.addActionListener(e -> new ListUI(user.getId()).setVisible(true));

        JPanel panel = new JPanel(new GridLayout(3, 1));
        panel.add(livresBtn);
        panel.add(favorisBtn);
        panel.add(listesBtn);

        add(panel);
    }
}

