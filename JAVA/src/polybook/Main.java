package polybook;

import polybook.ui.AdminFrame;
import javax.swing.*;

/**
 * PolyBook - Interface d'Administration
 * Classe principale pour lancer l'application Java d'administration
 * 
 * @author Équipe PolyBook
 * @version 1.0
 */
public class Main {
    
    public static void main(String[] args) {
        // Configuration du Look and Feel pour une interface moderne
        try {
            UIManager.setLookAndFeel(UIManager.getSystemLookAndFeel());
        } catch (Exception e) {
            System.err.println("Impossible de définir le Look and Feel: " + e.getMessage());
        }
        
        // Affichage du banner de démarrage
        System.out.println("=====================================");
        System.out.println("    PolyBook Administration v1.0     ");
        System.out.println("=====================================");
        System.out.println();
        
        // Test de connexion à la base de données
        System.out.println("🔄 Test de connexion à la base de données...");
        
        if (DatabaseConnection.testConnection()) {
            System.out.println("✅ Connexion réussie !");
            System.out.println("📊 Lancement de l'interface d'administration...");
            
            // Lancer l'interface graphique dans le thread EDT
            SwingUtilities.invokeLater(() -> {
                try {
                    AdminFrame frame = new AdminFrame();
                    frame.setVisible(true);
                    System.out.println("🚀 Interface lancée avec succès !");
                } catch (Exception e) {
                    System.err.println("❌ Erreur lors du lancement de l'interface: " + e.getMessage());
                    e.printStackTrace();
                }
            });
            
        } else {
            System.err.println("❌ Échec de la connexion à la base de données!");
            System.err.println();
            System.err.println("Vérifiez que :");
            System.err.println("1. ⚙️  MySQL est démarré");
            System.err.println("2. 🗄️  La base de données 'polybook' existe");
            System.err.println("3. 🔑 Les identifiants sont corrects");
            System.err.println("4. 📡 Le port 3306 est accessible");
            
            // Afficher une boîte de dialogue d'erreur
            SwingUtilities.invokeLater(() -> {
                JOptionPane.showMessageDialog(null, 
                    "❌ Impossible de se connecter à la base de données!\n\n" +
                    "Vérifiez votre configuration MySQL :\n" +
                    "• MySQL est-il démarré ?\n" +
                    "• La base 'polybook' existe-t-elle ?\n" +
                    "• Les identifiants sont-ils corrects ?",
                    "Erreur de connexion", 
                    JOptionPane.ERROR_MESSAGE);
            });
            
            System.exit(1);
        }
    }
}
