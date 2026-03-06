import { useState, useEffect } from "react";
import Header from "../../components/Header";
import { miscApi } from "../../api/client";

interface KBCategory {
  id: number;
  name: string;
  parent_id: number;
  sort_order: number;
}

interface KBArticle {
  id: number;
  category_id: number;
  title: string;
  content: string;
  views: number;
  created_at: string;
}

export default function KnowledgeBasePage() {
  const [categories, setCategories] = useState<KBCategory[]>([]);
  const [articles, setArticles] = useState<KBArticle[]>([]);
  const [selectedCat, setSelectedCat] = useState<number | null>(null);
  const [selectedArticle, setSelectedArticle] = useState<KBArticle | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    Promise.all([miscApi.kbCategories(), miscApi.kbArticles()])
      .then(([catRes, artRes]) => {
        setCategories((catRes.data as KBCategory[]) || []);
        setArticles((artRes.data as KBArticle[]) || []);
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  const filteredArticles = selectedCat
    ? articles.filter((a) => a.category_id === selectedCat)
    : articles;

  return (
    <>
      <Header title="Knowledge Base" />
      <div className="page">
        <div className="page-header">
          <div>
            <h2 className="page-title">Knowledge Base</h2>
            <p className="page-subtitle">{categories.length} categories, {articles.length} articles</p>
          </div>
        </div>
        {error && <div className="alert alert-error">{error}</div>}
        {loading ? (
          <div className="loading"><div className="spinner"></div></div>
        ) : selectedArticle ? (
          <div className="card">
            <div className="card-header">
              <h3 className="card-title">{selectedArticle.title}</h3>
              <button className="btn btn-secondary btn-sm" onClick={() => setSelectedArticle(null)}>← Back</button>
            </div>
            <div className="card-body">
              <div className="text-sm text-muted mb-2">
                Views: {selectedArticle.views} • {new Date(selectedArticle.created_at).toLocaleDateString()}
              </div>
              <div style={{ whiteSpace: "pre-wrap" }}>{selectedArticle.content}</div>
            </div>
          </div>
        ) : (
          <div style={{ display: "grid", gridTemplateColumns: "240px 1fr", gap: 20 }}>
            <div className="card">
              <div className="card-header"><h3 className="card-title">Categories</h3></div>
              <div className="card-body" style={{ padding: 0 }}>
                <button
                  className={`sidebar-link${!selectedCat ? " active" : ""}`}
                  onClick={() => setSelectedCat(null)}
                  style={{ width: "100%", textAlign: "left", border: "none", background: !selectedCat ? "#f1f5f9" : "transparent" }}
                >
                  All Articles
                </button>
                {categories.map((cat) => (
                  <button
                    key={cat.id}
                    className={`sidebar-link${selectedCat === cat.id ? " active" : ""}`}
                    onClick={() => setSelectedCat(cat.id)}
                    style={{ width: "100%", textAlign: "left", border: "none", background: selectedCat === cat.id ? "#f1f5f9" : "transparent" }}
                  >
                    {cat.name}
                  </button>
                ))}
              </div>
            </div>
            <div>
              {filteredArticles.length === 0 ? (
                <div className="card">
                  <div className="empty-state">
                    <div className="empty-state-icon">📚</div>
                    <div className="empty-state-title">No articles</div>
                    <div className="empty-state-text">Knowledge base articles will appear here.</div>
                  </div>
                </div>
              ) : (
                <div style={{ display: "grid", gap: 12 }}>
                  {filteredArticles.map((a) => (
                    <div key={a.id} className="card" style={{ cursor: "pointer" }} onClick={() => setSelectedArticle(a)}>
                      <div className="card-body">
                        <h4 style={{ marginBottom: 4 }}>{a.title}</h4>
                        <p className="text-sm text-muted">
                          {a.content.substring(0, 150)}...
                        </p>
                        <span className="text-sm text-muted">{a.views} views</span>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        )}
      </div>
    </>
  );
}
