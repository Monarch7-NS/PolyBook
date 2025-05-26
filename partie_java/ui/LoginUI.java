package partie_java.ui;

import partie_java.dao.UserDAO;
import partie_java.models.User;

import javax.swing.*;
import java.awt.*;

public class LoginUI extends JFrame {
    public LoginUI() {
        setTitle("Connexion PolyBook");
        setSize(400, 200);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);

        JPanel panel = new JPanel(new GridLayout(4, 1));
        JTextField emailField = new JTextField();
        JPasswordField passwordField = new JPasswordField();
        JButton loginBtn = new JButton("Se connecter");
        JLabel resultLabel = new JLabel("", SwingConstants.CENTER);

        panel.add(new JLabel("Email :"));
        panel.add(emailField);
        panel.add(new JLabel("Mot de passe :"));
        panel.add(passwordField);
        add(panel, BorderLayout.CENTER);
        add(loginBtn, BorderLayout.SOUTH);
        add(resultLabel, BorderLayout.NORTH);

        loginBtn.addActionListener(e -> {
            String email = emailField.getText();
            String password = new String(passwordField.getPassword());

            UserDAO dao = new UserDAO();
            User user = dao.getUserByEmailAndPassword(email, password);
            if (user != null) {

                dispose(); // Fermer la fenêtre Login

                new MainAppUI(user).setVisible(true); // Lancer le site

            } else {
                resultLabel.setText("❌ Identifiants incorrects.");
            }
        });
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new LoginUI().setVisible(true));
    }
}
