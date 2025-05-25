package polybook;

import java.sql.*;
import java.util.Properties;

/**
 * Gestionnaire de connexion à la base de données MySQL pour PolyBook
 * Configuration adaptée selon les paramètres PHP
 */
public class DatabaseConnection {
    
    // ✅ CONFIGURATION ADAPTÉE À VOS PARAMÈTRES PHP
    private static final String URL = "jdbc:mysql://localhost:3306/polybook";
    private static final String USERNAME = "root";
    private static final String PASSWORD = "root"; // ✅ Correspond à  $motdepasse
    
    private static Connection connection = null;
    private static boolean isConnected = false;
    
    /**
     * Obtient la connexion à la base de données (Singleton)
     */
    public static Connection getConnection() {
        try {
            if (connection == null || connection.isClosed() || !connection.isValid(5)) {
                createConnection();
            }
        } catch (SQLException e) {
            System.err.println("❌ Erreur lors de la vérification de la connexion: " + e.getMessage());
            createConnection();
        }
        return connection;
    }
    
    /**
     * Crée une nouvelle connexion à la base de données
     */
    private static void createConnection() {
        try {
            // Charger le driver MySQL
            Class.forName("com.mysql.cj.jdbc.Driver");
            
            // Propriétés de connexion optimisées
            Properties props = new Properties();
            props.setProperty("user", USERNAME);
            props.setProperty("password", PASSWORD);
            props.setProperty("useSSL", "false");
            props.setProperty("serverTimezone", "UTC");
            props.setProperty("allowPublicKeyRetrieval", "true");
            props.setProperty("useUnicode", "true");
            props.setProperty("characterEncoding", "UTF-8");
            
            // Établir la connexion
            connection = DriverManager.getConnection(URL, props);
            isConnected = true;
            
            System.out.println("🔗 Connexion à la base de données établie");
            System.out.println("📍 Serveur: localhost:3306");
            System.out.println("🗄️  Base de données: polybook");
            System.out.println("👤 Utilisateur: " + USERNAME);
            
        } catch (ClassNotFoundException e) {
            System.err.println("❌ Driver MySQL introuvable!");
            System.err.println("   Vérifiez que mysql-connector-java-8.0.33.jar est dans lib/");
            System.err.println("   Téléchargez-le depuis: https://dev.mysql.com/downloads/connector/j/");
            isConnected = false;
        } catch (SQLException e) {
            System.err.println("❌ Erreur de connexion à la base de données:");
            System.err.println("   Message: " + e.getMessage());
            System.err.println("   Code d'erreur: " + e.getErrorCode());
            System.err.println("   État SQL: " + e.getSQLState());
            System.err.println();
            System.err.println("🔧 Vérifications suggérées:");
            System.err.println("   1. MySQL est-il démarré ?");
            System.err.println("   2. La base 'polybook' existe-t-elle ?");
            System.err.println("   3. L'utilisateur 'root' avec mot de passe 'root' est-il correct ?");
            System.err.println("   4. Le port 3306 est-il accessible ?");
            isConnected = false;
        }
    }
    
    /**
     * Ferme la connexion à la base de données
     */
    public static void closeConnection() {
        try {
            if (connection != null && !connection.isClosed()) {
                connection.close();
                isConnected = false;
                System.out.println("🔌 Connexion fermée proprement");
            }
        } catch (SQLException e) {
            System.err.println("❌ Erreur lors de la fermeture: " + e.getMessage());
        }
    }
    
    /**
     * Teste la connexion à la base de données
     */
    public static boolean testConnection() {
        try {
            Connection conn = getConnection();
            if (conn != null && !conn.isClosed()) {
                // Test avec une requête simple sur la base polybook
                Statement stmt = conn.createStatement();
                ResultSet rs = stmt.executeQuery("SELECT COUNT(*) as total FROM user");
                
                boolean hasResult = rs.next();
                int userCount = hasResult ? rs.getInt("total") : 0;
                
                rs.close();
                stmt.close();
                
                if (hasResult) {
                    System.out.println("✅ Test de connexion réussi");
                    System.out.println("📊 Nombre d'utilisateurs dans la base: " + userCount);
                    return true;
                } else {
                    System.err.println("❌ Test de connexion échoué - Pas de résultat");
                    return false;
                }
            }
        } catch (SQLException e) {
            System.err.println("❌ Test de connexion échoué: " + e.getMessage());
            
            // Messages d'aide spécifiques selon l'erreur
            if (e.getMessage().contains("Access denied")) {
                System.err.println("   → Vérifiez vos identifiants (root/root)");
            } else if (e.getMessage().contains("Connection refused")) {
                System.err.println("   → MySQL n'est pas démarré ou port incorrect");
            } else if (e.getMessage().contains("Unknown database")) {
                System.err.println("   → La base de données 'polybook' n'existe pas");
            }
        }
        return false;
    }
    
    /**
     * Vérifie si la connexion est active
     */
    public static boolean isConnected() {
        return isConnected && connection != null;
    }
    
    /**
     * Affiche les informations de la base de données
     */
    public static void printDatabaseInfo() {
        try {
            Connection conn = getConnection();
            if (conn != null) {
                DatabaseMetaData metaData = conn.getMetaData();
                System.out.println("📊 Informations de la base de données:");
                System.out.println("   Nom: " + metaData.getDatabaseProductName());
                System.out.println("   Version: " + metaData.getDatabaseProductVersion());
                System.out.println("   Driver: " + metaData.getDriverName());
                System.out.println("   Version du driver: " + metaData.getDriverVersion());
                System.out.println("   URL: " + URL);
            }
        } catch (SQLException e) {
            System.err.println("❌ Impossible d'obtenir les infos de la DB: " + e.getMessage());
        }
    }
}
